<?php

namespace Wbc\ValuationBundle;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Psr\Log\LoggerInterface;
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
     * ValuationManager Constructor.
     *
     * @DI\InjectParams({
     * "entityManager" = @DI\Inject("doctrine.orm.default_entity_manager"),
     * "valuationCommand" = @DI\Inject("%valuation_command%"),
     * "valuationDiscountPercentage" = @DI\Inject("%valuation_discount_percentage%"),
     * "usdExchangeRate" = @DI\Inject("%usd_exchange_rate%"),
     * "logger" = @DI\Inject("logger")
     * })
     *
     * @param EntityManager   $entityManager
     * @param string          $valuationCommand
     * @param int             $valuationDiscountPercentage
     * @param float           $usdExchangeRate
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $entityManager, $valuationCommand, $valuationDiscountPercentage, $usdExchangeRate, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->valuationCommand = $valuationCommand;
        $this->valuationDiscountPercentage = $valuationDiscountPercentage;
        $this->usdExchangeRate = $usdExchangeRate;
        $this->logger = $logger;
    }

    public function setPrice(Valuation $valuation)
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
                $price = $this->roundUpToAny(intval($output['price']));

                if ($price && $price > self::MIN_ALLOWABLE_PRICE) {
                    $discounts = $this->getValuationConfigurationDiscounts($valuation);

                    foreach ($discounts as $discount) {
                        $price = $price + $price * intval($discount['discount']) / 100;
                    }

                    $price = $price + $price * $this->valuationDiscountPercentage / 100;
                    $valuation->setPriceOnline($price);
                    $this->entityManager->flush();
                }
            }
        }
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

        $trainingData = array_merge($dubizzleTrainingData, $manheimTrainingData);

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
                                            WHERE
                                                year BETWEEN :yearMin AND :yearMax
                                            AND mileage BETWEEN :mileageMin AND :mileageMax
                                            AND model_id = :modelId
                                            AND source = :source
                                            ORDER BY FIELD(c_year, :year)
                                            LIMIT :limit
                                            ');

        $statement->bindParam(':exchangeRate', $exchangeRate);
        $statement->bindValue(':source', $source, \PDO::PARAM_STR);
        $statement->bindValue(':year', $year, \PDO::PARAM_INT);
        $statement->bindValue(':yearMin', $year - 1, \PDO::PARAM_INT);
        $statement->bindValue(':yearMax', $year + 1, \PDO::PARAM_INT);
        $statement->bindValue(':mileageMin', $mileage - 20000, \PDO::PARAM_INT);
        $statement->bindValue(':mileageMax', $mileage + 20000, \PDO::PARAM_INT);
        $statement->bindValue(':modelId', $modelId, \PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param Valuation $valuation
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getValuationConfigurationDiscounts(Valuation $valuation)
    {
        $year = $valuation->getVehicleYear();
        $makeId = $valuation->getVehicleMake()->getId();
        $modelId = $valuation->getVehicleModel()->getId();

        $connection = $this->entityManager->getConnection();
        $statement = $connection->prepare('
                SELECT discount
                FROM valuation_configuration
                WHERE (vehicle_year = :year AND vehicle_make_id IS NULL AND vehicle_model_id IS NULL)
                OR (vehicle_make_id = :makeId AND vehicle_model_id IS NULL AND vehicle_year IS NULL)
                OR (vehicle_model_id = :modelId AND vehicle_year IS NULL)
                OR (vehicle_model_id = :modelId AND vehicle_year = :year)
        ');
        $statement->bindValue(':year', $year, \PDO::PARAM_INT);
        $statement->bindValue(':makeId', $makeId, \PDO::PARAM_INT);
        $statement->bindValue(':modelId', $modelId, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    private function roundUpToAny($n, $x = 5)
    {
        return (ceil($n) % $x === 0) ? ceil($n) : round(($n + $x / 2) / $x) * $x;
    }
}
