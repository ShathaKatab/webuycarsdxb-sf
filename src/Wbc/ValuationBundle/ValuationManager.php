<?php

namespace Wbc\ValuationBundle;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Wbc\CrawlerBundle\Entity\ClassifiedsAd;
use Wbc\ValuationBundle\Entity\TrainingData;
use Wbc\ValuationBundle\Entity\Valuation;

/**
 * Class ValuationManager.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.valuation_manager")
 */
class ValuationManager
{
    const MIN_ALLOWABLE_PRICE = 500;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $valuationCommand;

    /**
     * @var int
     */
    private $valuationDiscountPercentage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var float
     */
    private $usdExchangeRate;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ValuationManager Constructor.
     *
     * @DI\InjectParams({
     * "entityManager" = @DI\Inject("doctrine.orm.default_entity_manager"),
     * "valuationCommand" = @DI\Inject("%valuation_command%"),
     * "container" = @DI\Inject("service_container"),
     * "logger" = @DI\Inject("logger")
     * })
     *
     * @param EntityManager      $entityManager
     * @param string             $valuationCommand
     * @param LoggerInterface    $logger
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $entityManager, $valuationCommand, ContainerInterface $container, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->valuationCommand = $valuationCommand;
        $this->logger = $logger;
        $this->container = $container;
        $this->valuationDiscountPercentage = floatval($this->container->get('craue_config')->get('valuationDiscountPercentage'));
        $this->usdExchangeRate = floatval($this->container->get('craue_config')->get('usdExchangeRate'));
    }

    public function setPrice(Valuation $valuation)
    {
        $price = $this->getPriceFromAverages($valuation);
//        $price = $this->getPriceFromAI($valuation);
        $this->setValuationPrice($valuation, $price);
    }

    protected function getPriceFromAverages(Valuation $valuation)
    {
        $modelId = intval($valuation->getVehicleModel()->getId());
        $year = intval($valuation->getVehicleYear());

        $averages = ['price' => 0, 'mileage' => 0];

        $averagesDubizzle = $this->getAverages($modelId, $year, ClassifiedsAd::SOURCE_DUBIZZLE);
        $averagesManheim = $this->getAverages($modelId, $year, ClassifiedsAd::SOURCE_MANHEIM, $this->usdExchangeRate);

        if ($averagesDubizzle) {
            $averages = ['price' => $averagesDubizzle['avg_price'], 'mileage' => $averagesDubizzle['avg_mileage']];
        }

        if ($averagesManheim) {
            if ($averages['price'] && $averages['mileage']) {
                $averages['price'] = ($averages['price'] + $averagesManheim['avg_price']) / 2;
                $averages['mileage'] = ($averages['mileage'] + $averagesManheim['avg_mileage']) / 2;
            } else {
                $averages = ['price' => $averagesManheim['avg_price'], 'mileage' => $averagesManheim['avg_mileage']];
            }
        }

        $mileage = $valuation->getVehicleMileage();
        $averagePrice = $averages['price'];
        $averageMileage = $averages['mileage'];
        $price = $averagePrice;
        $mileagePercentage = 0;

        $downMileageInterval = floatval($this->container->get('craue_config')->get('downMileageInterval'));
        $downMileagePercentage = floatval($this->container->get('craue_config')->get('downMileagePercentage'));
        $upMileageInterval = floatval($this->container->get('craue_config')->get('upMileageInterval'));
        $upMileagePercentage = floatval($this->container->get('craue_config')->get('upMileagePercentage'));

        if ($mileage < $averageMileage) {
            $avgCoefficient = floor($averageMileage / $downMileageInterval);
            $mileageCoefficient = ceil($mileage / $downMileageInterval);
            $coefficient = $avgCoefficient - $mileageCoefficient;
            $mileagePercentage = $coefficient * $downMileagePercentage;
        } elseif ($mileage > $averageMileage) {
            $avgCoefficient = floor($averageMileage / $upMileageInterval);
            $mileageCoefficient = ceil($mileage / $upMileageInterval);
            $coefficient = $mileageCoefficient - $avgCoefficient;
            $mileagePercentage = $coefficient * $upMileagePercentage;
        }

        return $price + $price * $mileagePercentage / 100;
    }

    protected function getAverages($modelId, $year, $source, $exchangeRate = 1)
    {
        $connection = $this->entityManager->getConnection();
        $statement = $connection->prepare('SELECT
                                                CAST(year AS UNSIGNED) AS year,
                                                AVG(CAST(mileage AS UNSIGNED)) AS avg_mileage,
                                                AVG(CAST(price AS UNSIGNED)) * :exchangeRate AS avg_price
                                            FROM valuation_training_data
                                            WHERE year = :year
                                            AND model_id = :modelId
                                            AND source = :source
                                            GROUP BY year
                                            ');
        $statement->bindParam(':exchangeRate', $exchangeRate);
        $statement->bindValue(':year', $year, \PDO::PARAM_INT);
        $statement->bindValue(':modelId', $modelId, \PDO::PARAM_INT);
        $statement->bindParam(':source', $source, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }

    protected function getPriceFromAI(Valuation $valuation)
    {
        $filePath = $this->generateTrainingDataFile($valuation);
        $output = null;
        //No Training Data available, just bounce
        if (!$filePath) {
            return;
        }

        $command = strtr($this->valuationCommand, ['%filePath%' => escapeshellarg($filePath)]);

        try {
            $process = new Process($command);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            $output = $process->getOutput();
        } catch (ProcessFailedException $e) {
            $this->logger->critical($e->getMessage());
        }

        if ($output) {
            $output = json_decode($output, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($output['price'])) {
                return floatval($output['price']);
            }
        }

        return;
    }

    protected function generateTrainingDataFile(Valuation $valuation)
    {
        $modelId = intval($valuation->getVehicleModel()->getId());
        $makeId = intval($valuation->getVehicleMake()->getId());
        $year = intval($valuation->getVehicleYear());
        $mileage = intval($valuation->getVehicleMileage());

        $color = strtolower($valuation->getVehicleColor());

        if (isset(TrainingData::$colors[$color])) {
            $color = TrainingData::$colors[$color];
        } else {
            $color = TrainingData::$colors['other'];
        }

        $bodyCondition = strtolower($valuation->getVehicleBodyCondition());

        if (isset(TrainingData::$bodyConditions[$bodyCondition])) {
            $bodyCondition = TrainingData::$bodyConditions[$bodyCondition];
        } else {
            $bodyCondition = TrainingData::$bodyConditions['other'];
        }

        $testData = [
            'a_make' => $makeId,
            'b_model' => $modelId,
            'c_year' => $year,
            'd_mileage' => $mileage,
            'f_color' => intval($color),
            'g_body_condition' => intval($bodyCondition),
            'z_price' => 0,
        ];

        $dubizzleTrainingData = $this->getValuationData(ClassifiedsAd::SOURCE_DUBIZZLE, $year, $mileage, $modelId);
        $manheimTrainingData = $this->getValuationData(ClassifiedsAd::SOURCE_MANHEIM, $year, $mileage, $modelId, $this->usdExchangeRate);

        $trainingData = array_merge($manheimTrainingData, $dubizzleTrainingData);

        if (!$trainingData) {
            return;
        }

        array_walk($trainingData, function (&$item) {
            foreach ($item as $key => $_item) {
                $item[$key] = intval($_item);
            }
        });

        $trainingData[] = $testData;

        $fs = new Filesystem();
        $valuationDir = '/tmp/wbc-valuations/';

        if (!$fs->exists($valuationDir)) {
            $fs->mkdir($valuationDir);
        }

        $filePath = sprintf('%s%s.json', $valuationDir, $valuation->getId());

        $fs->dumpFile($filePath, json_encode($trainingData), 0777);

        return $filePath;
    }

    /**
     * @param string $source
     * @param int    $year
     * @param int    $mileage
     * @param int    $modelId
     * @param int    $exchangeRate
     * @param int    $limit
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getValuationData($source, $year, $mileage, $modelId, $exchangeRate = 1, $limit = 100)
    {
        $connection = $this->entityManager->getConnection();
        $statement = $connection->prepare('SELECT
                                                CAST(make_id AS UNSIGNED) AS a_make,
                                                CAST(model_id AS UNSIGNED) AS b_model,
                                                CAST(year AS UNSIGNED) AS c_year,
                                                CAST(mileage AS UNSIGNED) AS d_mileage,
                                                CAST(color AS UNSIGNED) AS f_color,
                                                CAST(body_condition AS UNSIGNED) AS g_body_condition,
                                                (CAST(price AS UNSIGNED) * :exchangeRate) AS z_price
                                            FROM valuation_training_data
                                            WHERE year BETWEEN :yearMin AND :year
                                            AND model_id = :modelId
                                            AND mileage BETWEEN :mileageMin AND :mileageMax
                                            AND source = :source
                                            ORDER BY d_mileage ASC, FIELD(c_year, :year, :yearMin)
                                            LIMIT :limit
                                            ');

        $statement->bindParam(':exchangeRate', $exchangeRate);
        $statement->bindValue(':source', $source, \PDO::PARAM_STR);
        $statement->bindValue(':year', $year, \PDO::PARAM_INT);
        $statement->bindValue(':yearMin', $year - 1, \PDO::PARAM_INT);
        $statement->bindValue(':mileage', $mileage, \PDO::PARAM_INT);
        $statement->bindValue(':mileageMin', $mileage - 5000, \PDO::PARAM_INT);
        $statement->bindValue(':mileageMax', $mileage + 20000, \PDO::PARAM_INT);
        $statement->bindValue(':modelId', $modelId, \PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param Valuation $valuation
     *
     * @return float
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getValuationConfigurationDiscount(Valuation $valuation)
    {
        $makeId = $valuation->getVehicleMake()->getId();
        $year = $valuation->getVehicleYear();
        $modelId = $valuation->getVehicleModel()->getId();
        $color = strtolower($valuation->getVehicleColor());
        $bodyCondition = strtolower($valuation->getVehicleBodyCondition());
        $discount = 0.0;

        $connection = $this->entityManager->getConnection();
        $statement = $connection->prepare('
                SELECT vehicle_make_id, vehicle_model_id, vehicle_year, vehicle_color, vehicle_body_condition, discount
                FROM valuation_configuration
                ');
        $statement->execute();

        $configs = $statement->fetchAll();

        foreach ($configs as $config) {
            $bitwise = 0;

            if ($config['vehicle_make_id'] == $makeId) {
                $bitwise += 1;
            }

            if ($config['vehicle_model_id'] == $modelId) {
                $bitwise += 2;
            }

            if ($config['vehicle_year'] == $year) {
                $bitwise += 4;
            }

            if ($config['vehicle_color'] == $color) {
                $bitwise += 8;
            }

            if ($config['vehicle_body_condition'] == $bodyCondition) {
                $bitwise += 16;
            }

            switch ($bitwise) {
                case 1://matches make_id
                    $discount += $config['discount'];
                    break;
                case 2://matches model_id
                    $discount += $config['discount'];
                    break;
                case 4://matches year
                    $discount += $config['discount'];
                    break;
                case 8: //matches color
                    $discount += $config['discount'];
                    break;
                case 16: //matches body condition
                    $discount += $config['discount'];
                    break;
                case (1 + 2 + 4 + 8 + 16): //matches everything
                    $discount += $config['discount'];
                    break;
                default:
                    if (($bitwise & (2 + 4 + 8 + 16)) == (2 + 4 + 8 + 16)) {
                        //matches model, year, color, body_condition
                        $discount += $config['discount'];
                    } elseif (($bitwise & (2 + 4)) == (2 + 4)) {
                        //matches model, year
                        $discount += $config['discount'];
                    } elseif (($bitwise & (2 + 8)) == (2 + 8)) {
                        //matches model, color
                        $discount += $config['discount'];
                    } elseif (($bitwise & (2 + 8 + 16)) == (2 + 8 + 16)) {
                        //matches model, color, body_condition
                        $discount += $config['discount'];
                    } elseif (($bitwise & (2 + 16)) == (2 + 16)) {
                        //matches model, body_condition
                        $discount += $config['discount'];
                    } elseif (($bitwise & (4 + 8 + 16)) == (4 + 8 + 16)) {
                        //matches year, color, body_condition
                        $discount += $config['discount'];
                    } elseif (($bitwise & (4 + 8)) == (4 + 8)) {
                        //matches year, color
                        $discount += $config['discount'];
                    } elseif (($bitwise & (4 + 16)) == (4 + 16)) {
                        //matches year, body_condition
                        $discount += $config['discount'];
                    } elseif (($bitwise & (8 + 16)) == (8 + 16)) {
                        //matches color, body_condition
                        $discount += $config['discount'];
                    } elseif ($bitwise === 3) {
                        //matches model and make
                        $discount += $config['discount'];
                    }
            }
        }

        return floatval($discount);
    }

    private function roundUpToAny($n, $x = 5)
    {
        return (ceil($n) % $x === 0) ? ceil($n) : round(($n + $x / 2) / $x) * $x;
    }

    private function setValuationPrice(Valuation $valuation, $price = null)
    {
        if ($price && $price > self::MIN_ALLOWABLE_PRICE) {
            $discount = $this->getValuationConfigurationDiscount($valuation);

            $price = $price + $price * $discount / 100;

            $price = $this->roundUpToAny($price + $price * $this->valuationDiscountPercentage / 100);

            if ($price < self::MIN_ALLOWABLE_PRICE) {
                $price = 0.0;
            }

            $valuation->setPriceOnline($price);

            $this->entityManager->flush();
        }
    }
}
