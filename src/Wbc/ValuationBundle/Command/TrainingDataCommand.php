<?php

namespace Wbc\ValuationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wbc\CrawlerBundle\Entity\ClassifiedsAd;
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
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('valuation:generate:training-data')
            ->addOption('overwrite', null, InputOption::VALUE_OPTIONAL, 'Either true or false; if false existing rows will be ignored')
            ->setDescription('Generates machine learning test data from Classifieds Ads.');
    }

    /**
     * Currently works for Dubizzle only.
     *
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Generating Training Data for Machine Learning</info>');
        $this->entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $output->writeln('<comment>Working on Training Data for Dubizzle.com</comment>');
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
            $output->writeln(sprintf('<fg=magenta>Setting Data for: Make => %s, Model => %s</>', $mappingMakeModel['makeName'], $mappingMakeModel['modelName']));
            $statement = $connection->prepare('SELECT id, year, mileage, exterior_color as color, body_condition, source, price
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

                $trainingData = new TrainingData(
                    $make,
                    $model,
                    $classifiedsAd,
                    $ad['year'],
                    $ad['mileage'],
                    $ad['color'],
                    $ad['body_condition'],
                    $ad['price'],
                    $ad['source']
                );

                $this->entityManager->persist($trainingData);
            }
        }

        $this->entityManager->flush();
        $output->writeln('<comment>Done with Training Data for Dubizzle.com</comment>');
    }
}
