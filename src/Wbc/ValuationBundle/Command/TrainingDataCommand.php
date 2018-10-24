<?php

declare(strict_types=1);

namespace Wbc\ValuationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wbc\CrawlerBundle\Entity\ClassifiedsAd;
use Wbc\InventoryBundle\Entity\Inventory;
use Wbc\ValuationBundle\Entity\TrainingData;

/**
 * Class TrainingDataCommand.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class TrainingDataCommand extends ContainerAwareCommand
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('valuation:generate:training-data')
            ->addArgument('source', InputArgument::REQUIRED, 'Source of the Classifieds Ad; manheim, dubizzle, inventory')
            ->addOption('overwrite', null, InputOption::VALUE_OPTIONAL, 'Either true or false; if false existing rows will be ignored')
            ->setDescription('Generates machine learning test data from Classifieds Ads or Inventory.');
    }

    /**
     * Currently works for Dubizzle only.
     *
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('<info>Generating Training Data for Machine Learning</info>');
        $this->entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->output = $output;
        $source = $input->getArgument('source');

        if (!in_array($source, ['manheim.com', 'dubizzle.com', 'inventory'], true)) {
            throw new \RuntimeException(sprintf('Source: %s is invalid! Valid sources are; manheim.com, dubizzle.com, inventory', $source));
        }

        if ('dubizzle.com' === $source) {
            $this->dubizzleTrainingData();
        }

        if ('manheim.com' === $source) {
            $this->manheimTrainingData();
        }

        if ('inventory' === $source) {
            $this->inventoryTrainingData();
        }

        $output->writeln('<info>Done Generating Training Data for Machine Learning.</info>');
    }

    private function manheimTrainingData(): void
    {
        $this->output->writeln('<comment>Working on Training Data for Manheim.com</comment>');
        //Manheim
        $connection = $this->entityManager->getConnection();
        $statement = $connection->prepare('SELECT mmakes.manheim_make_name AS makeName, mmodels.manheim_model_name AS modelName, mmakes.make_id AS makeId, mmodels.model_id AS modelId
                                              FROM mapping_models mmodels
                                                INNER JOIN vehicle_model vmodel ON vmodel.id = mmodels.model_id
                                                INNER JOIN mapping_makes mmakes ON mmakes.make_id = vmodel.make_id AND mmakes.manheim_make_name IS NOT NULL
                                              WHERE mmodels.manheim_model_name IS NOT NULL
                                              GROUP BY mmakes.manheim_make_name, mmodels.manheim_model_name');
        $statement->execute();
        $mappingMakesModels = $statement->fetchAll();

        $row = 1;

        foreach ($mappingMakesModels as $mappingMakeModel) {
            $this->output->writeln(sprintf('<fg=magenta>Setting Data for: Make => %s, Model => %s</>', $mappingMakeModel['makeName'], $mappingMakeModel['modelName']));

            $statement = $connection->prepare('SELECT id, year, mileage, exterior_color as color, body_condition, source, price, currency
                                                FROM crawler_classifieds_ad
                                                WHERE vehicle_make = :makeName
                                                AND vehicle_model LIKE :modelName
                                                AND source = :source');
            $statement->bindValue(':makeName', $mappingMakeModel['makeName']);
            $statement->bindValue(':modelName', $mappingMakeModel['modelName'].'%');
            $statement->bindValue(':source', ClassifiedsAd::SOURCE_MANHEIM);
            $statement->execute();

            $ads = $statement->fetchAll();

            foreach ($ads as $ad) {
                $make = $this->entityManager->getReference('WbcVehicleBundle:Make', $mappingMakeModel['makeId']);
                $model = $this->entityManager->getReference('WbcVehicleBundle:Model', $mappingMakeModel['modelId']);
                $mileage = round((int) ($ad['mileage']) * 1.60934);
                $classifiedsAd = $this->entityManager->getReference('WbcCrawlerBundle:ClassifiedsAd', $ad['id']);
                $trainingData = new TrainingData($make, $model, $ad['year'], $mileage, $ad['color'], $ad['body_condition'], $ad['price'], $ad['source']);
                $trainingData->setCrawlerClassifiedsAd($classifiedsAd);
                $trainingData->setCurrency($ad['currency']);
                $this->entityManager->persist($trainingData);
                ++$row;

                if (0 === $row % 200) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                    gc_collect_cycles();
                }
            }
        }

        $this->entityManager->flush();
        $this->output->writeln('<comment>Done with Training Data for Manheim.com</comment>');
    }

    private function dubizzleTrainingData(): void
    {
        $this->output->writeln('<comment>Working on Training Data for Dubizzle.com</comment>');
        //Dubizzle
        $connection = $this->entityManager->getConnection();
        $statement = $connection->prepare('SELECT mmakes.dubizzle_make_name AS makeName, mmodels.dubizzle_model_name AS modelName, mmakes.make_id AS makeId, mmodels.model_id AS modelId
                                              FROM mapping_models mmodels
                                                INNER JOIN vehicle_model vmodel ON vmodel.id = mmodels.model_id
                                                INNER JOIN mapping_makes mmakes ON mmakes.make_id = vmodel.make_id AND mmakes.dubizzle_make_name IS NOT NULL
                                              WHERE mmodels.dubizzle_model_name IS NOT NULL
                                              GROUP BY mmakes.dubizzle_make_name, mmodels.dubizzle_model_name');
        $statement->execute();

        $mappingMakesModels = $statement->fetchAll();

        foreach ($mappingMakesModels as $mappingMakeModel) {
            $this->output->writeln(sprintf('<fg=magenta>Setting Data for: Make => %s, Model => %s</>', $mappingMakeModel['makeName'], $mappingMakeModel['modelName']));

            $statement = $connection->prepare('SELECT id, year, mileage, exterior_color as color, body_condition, source, price, currency
                                                FROM crawler_classifieds_ad
                                                WHERE vehicle_make = :makeName AND vehicle_model = :modelName AND source = :source');
            $statement->bindValue(':makeName', $mappingMakeModel['makeName']);
            $statement->bindValue(':modelName', $mappingMakeModel['modelName']);
            $statement->bindValue(':source', ClassifiedsAd::SOURCE_DUBIZZLE);
            $statement->execute();

            $ads = $statement->fetchAll();

            foreach ($ads as $ad) {
                $make = $this->entityManager->getReference('WbcVehicleBundle:Make', $mappingMakeModel['makeId']);
                $model = $this->entityManager->getReference('WbcVehicleBundle:Model', $mappingMakeModel['modelId']);
                $classifiedsAd = $this->entityManager->getReference('WbcCrawlerBundle:ClassifiedsAd', $ad['id']);
                $trainingData = new TrainingData($make, $model, $ad['year'], $ad['mileage'], $ad['color'], $ad['body_condition'], $ad['price'], $ad['source']);
                $trainingData->setCrawlerClassifiedsAd($classifiedsAd);
                $trainingData->setCurrency($ad['currency']);
                $this->entityManager->persist($trainingData);
            }
        }

        $this->entityManager->flush();
        $this->output->writeln('<comment>Done with Training Data for Dubizzle.com</comment>');
    }

    private function inventoryTrainingData(): void
    {
        $this->output->writeln('<comment>Working on Training Data from webuycarsdxb.com Inventory</comment>');
        //Inventory
        $connection = $this->entityManager->getConnection();
        $statement = $connection->prepare('SELECT make.name AS makeName, model.name AS modelName, make.id AS makeId, model.id AS modelId
                                              FROM vehicle_model AS model
                                              INNER JOIN vehicle_make AS make ON model.make_id = make.id');
        $statement->execute();
        $makesModels = $statement->fetchAll();

        foreach ($makesModels as $makeModel) {
            $this->output->writeln(sprintf('<fg=magenta>Setting Data for: Make => %s, Model => %s</>', $makeModel['makeName'], $makeModel['modelName']));

            $statement = $connection->prepare('SELECT inv.id, i.vehicle_year, i.vehicle_mileage, i.vehicle_color, i.vehicle_body_condition, d.price_purchased
                                                FROM inventory inv
                                                INNER JOIN inspection i ON i.id = inv.inspection_id
                                                WHERE i.vehicle_model_id = :modelId
                                                AND inv.price_purchased > 0');

            $statement->bindValue(':modelId', $makeModel['modelId']);
            $statement->execute();
            $inventory = $statement->fetchAll();

            foreach ($inventory as $inv) {
                $make = $this->entityManager->getReference('WbcVehicleBundle:Make', $makeModel['makeId']);
                $model = $this->entityManager->getReference('WbcVehicleBundle:Model', $makeModel['modelId']);

                $trainingData = new TrainingData($make, $model, $inv['vehicle_year'], $inv['vehicle_mileage'], $inv['vehicle_color'], $inv['vehicle_body_condition'], $inv['price_purchased'], ClassifiedsAd::SOURCE_INVENTORY);
                $trainingData->setInventory($this->entityManager->getReference(Inventory::class, $inv['id']));
                $trainingData->setCurrency(ClassifiedsAd::CURRENCY_AED);
                $this->entityManager->persist($trainingData);
            }
        }

        $this->entityManager->flush();
        $this->output->writeln('<comment>Done with Training Data from webuycarsdxb.com Inventory</comment>');
    }
}
