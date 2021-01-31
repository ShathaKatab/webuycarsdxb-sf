<?php

declare(strict_types=1);

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
     * @var int
     */
    private $pricePercentageForAllCars;


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
     * @param EntityManager $entityManager
     * @param string $valuationCommand
     * @param ContainerInterface $container
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $entityManager, string $valuationCommand, ContainerInterface $container, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->valuationCommand = $valuationCommand;
        $this->logger = $logger;
        $this->container = $container;
        $this->valuationDiscountPercentage = (float) ($this->container->get('craue_config')->get('valuationDiscountPercentage'));
        $this->pricePercentageForAllCars = (float) ($this->container->get('craue_config')->get('pricePercentageForAllCars'));
        $this->usdExchangeRate = (float) ($this->container->get('craue_config')->get('usdExchangeRate'));
    }

    public function setPrice(Valuation $valuation)
    {
        $price = $this->getPriceFromAverages($valuation);
//        $price = $this->getPriceFromAI($valuation);
        $this->setValuationPrice($valuation, $price);
    }

    protected function getPriceFromAverages(Valuation $valuation)
    {
        $modelId = (int) ($valuation->getVehicleModel()->getId());
        $year = (int) ($valuation->getVehicleYear());

        $averages = ['price' => 0, 'mileage' => 0];

        $averagesDubizzle = $this->getAverages($modelId, $year, ClassifiedsAd::SOURCE_DUBIZZLE);
        $averagesManheim = $this->getAverages($modelId, $year, ClassifiedsAd::SOURCE_MANHEIM, $this->usdExchangeRate);
        $averagesInspection = $this->getAverages($modelId, $year, ClassifiedsAd::SOURCE_INSPECTION);

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

        if ($averagesInspection) {
            //Hassan wants to add 20% to the price from Inspection
            $averagesInspection['avg_price'] = $averagesInspection['avg_price'] + $averagesInspection['avg_price'] * 0.2;

            if ($averages['price'] && $averages['mileage']) {
                $averages['price'] = ($averages['price'] + $averagesInspection['avg_price']) / 2;
                $averages['mileage'] = ($averages['mileage'] + $averagesInspection['avg_mileage']) / 2;
            } else {
                $averages = ['price' => $averagesInspection['avg_price'], 'mileage' => $averagesInspection['avg_mileage']];
            }
        }

        $option = $valuation->getVehicleOption();
        $bodyCondition = $valuation->getVehicleBodyCondition();
        $mileage = $valuation->getVehicleMileage();
        $averagePrice = $averages['price'];
        $averageMileage = $averages['mileage'];
        $price = $averagePrice;
        $mileagePercentage = 0;
        $optionPercentage = 0;
        $bodyConditionPercentage = 0;

        $downMileageInterval = (float) ($this->container->get('craue_config')->get('downMileageInterval'));
        $downMileagePercentage = (float) ($this->container->get('craue_config')->get('downMileagePercentage'));
        $upMileageInterval = (float) ($this->container->get('craue_config')->get('upMileageInterval'));
        $upMileagePercentage = (float) ($this->container->get('craue_config')->get('upMileagePercentage'));

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

        switch ($option) {
            case 'mid':
                $optionPercentage = (float) ($this->container->get('craue_config')->get('optionMidPercentage'));
                break;
            case 'full':
                $optionPercentage = (float) ($this->container->get('craue_config')->get('optionFullPercentage'));
                break;
            case 'basic':
                $optionPercentage = (float) ($this->container->get('craue_config')->get('optionBasicPercentage'));
                break;
        }

        switch ($bodyCondition) {
            case 'good':
                $bodyConditionPercentage = (float) ($this->container->get('craue_config')->get('bodyConditionGoodPercentage'));
                break;
            case 'fair':
                $bodyConditionPercentage = (float) ($this->container->get('craue_config')->get('bodyConditionFairPercentage'));
                break;
            case 'excellent':
                $bodyConditionPercentage = (float) ($this->container->get('craue_config')->get('bodyConditionExcellentPercentage'));
                break;
        }

        return $price + ($price * $mileagePercentage + $price * $optionPercentage + $price * $bodyConditionPercentage) / 100;
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
            if (JSON_ERROR_NONE === json_last_error() && isset($output['price'])) {
                return (float) ($output['price']);
            }
        }
    }

    protected function generateTrainingDataFile(Valuation $valuation)
    {
        $modelId = (int) ($valuation->getVehicleModel()->getId());
        $makeId = (int) ($valuation->getVehicleMake()->getId());
        $year = (int) ($valuation->getVehicleYear());
        $mileage = (int) ($valuation->getVehicleMileage());

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
            'f_color' => (int) $color,
            'g_body_condition' => (int) $bodyCondition,
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
                $item[$key] = (int) $_item;
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
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array
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
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return float
     */
    private function getValuationConfigurationDiscount(Valuation $valuation)
    {
        $makeId = $valuation->getVehicleMake()->getId();
        $year = $valuation->getVehicleYear();
        $modelId = $valuation->getVehicleModel()->getId();
        $modelTypeId = $valuation->getVehicleModelType()->getId();
        $color = strtolower($valuation->getVehicleColor() ?: '');
        $bodyCondition = strtolower($valuation->getVehicleBodyCondition() ?: '');
        $discount = 0.0;

        $connection = $this->entityManager->getConnection();
        $statement = $connection->prepare('
                SELECT vehicle_make_id, vehicle_model_id, vehicle_model_type_id, vehicle_year, vehicle_color, vehicle_body_condition, discount
                FROM valuation_configuration 
                WHERE active = :isActive
                ');
        $statement->bindValue(':isActive', true, \PDO::PARAM_BOOL);
        $statement->execute();

        $configs = $statement->fetchAll();

        foreach ($configs as $config) {
            if (null === $config['vehicle_make_id']
                && null === $config['vehicle_model_id']
                && null === $config['vehicle_model_type_id']
                && null === $config['vehicle_year']
                && null === $config['vehicle_color']
                && null === $config['vehicle_body_condition']) {
                continue;
            }

            if (null !== $config['vehicle_make_id'] && (int) ($config['vehicle_make_id']) !== $makeId) {
                continue;
            }

            if (null !== $config['vehicle_model_id'] && (int) ($config['vehicle_model_id']) !== $modelId) {
                continue;
            }

//            if (null !== $config['vehicle_model_type_id'] && (int) ($config['vehicle_model_type_id']) !== $modelTypeId) {
//                continue;
//            }

            if (null !== $config['vehicle_year'] && (int) ($config['vehicle_year']) !== $year) {
                continue;
            }

            if (null !== $config['vehicle_color'] && $config['vehicle_color'] !== $color) {
                continue;
            }

            if (null !== $config['vehicle_body_condition'] && $config['vehicle_body_condition'] !== $bodyCondition) {
                continue;
            }

            $discount += (float) ($config['discount']);
        }

        return (float) $discount;
    }

    private function roundUpToAny($n, $x = 5)
    {
        return (0 === ceil($n) % $x) ? ceil($n) : round(($n + $x / 2) / $x) * $x;
    }

    private function setValuationPrice(Valuation $valuation, $price = null)
    {
        $valuation->setActualPrice($price);

        if ($price && $price > self::MIN_ALLOWABLE_PRICE) {
            $discount = $this->getValuationConfigurationDiscount($valuation);

            if (isset($discount) && $discount > 0 && $discount > 100)
                $price=$discount;
            else
                $price = $price + $price * $discount / 100;

            $price = $price + ($price * $this->pricePercentageForAllCars / 100);

            $price = $this->roundUpToAny($price + $price * $this->valuationDiscountPercentage / 100);

            if ($price < self::MIN_ALLOWABLE_PRICE) {
                $price = 0.0;
            }

            $valuation->setDiscountPercentage($this->pricePercentageForAllCars);
            $valuation->setPriceOnline($price);
        }

        $this->entityManager->flush();
    }
}
