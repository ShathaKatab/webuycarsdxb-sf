<?php

namespace Wbc\CrawlerBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Wbc\CrawlerBundle\Entity\ClassifiedsAd;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Wbc\CrawlerBundle\Entity\ClassifiedsMake;
use Wbc\CrawlerBundle\Entity\ClassifiedsModel;
use Wbc\CrawlerBundle\Entity\ClassifiedsModelType;
use function Stringy\create as s;
/**
 * Class ManheimCommand.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ManheimCommand extends ClassifiedsCommand
{
    protected $url = 'https://gapi-prod.aws.manheim.com/gateway/';
    protected $source = ClassifiedsAd::SOURCE_MANHEIM;
    protected $siteName = 'Manheim';
    protected $years;

    protected function configure()
    {
        $this->setName('crawler:manheim:crawl')
            ->setDescription('Command to crawl manheim.com; it crawls manheim.com Models and manheim.com ADS')
            ->addArgument('type', InputArgument::REQUIRED, sprintf('Choose the type to crawl; "%s"', implode('","', $this->types)))
            ->addOption('overwrite', null, InputOption::VALUE_OPTIONAL, 'Either true or false; if false existing rows will be ignored');
    }

    protected function processAds()
    {
        $this->outputInterface->writeln(sprintf('<info>Crawling Ads from %s (%s)</info>', $this->siteName, $this->source));
        $connection = $this->entityManager->getConnection();

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('modelType')
            ->from('WbcCrawlerBundle:ClassifiedsModelType', 'modelType')
            ->innerJoin('WbcCrawlerBundle:ClassifiedsModel', 'model', 'WITH', 'modelType.model = model')
            ->innerJoin('WbcCrawlerBundle:ClassifiedsMake', 'make', 'WITH', 'model.make = make AND make.source = :source')
//            ->where('modelType.id > 13518')
            ->setParameter(':source', $this->source)
        ;

        $modelTypes = $queryBuilder->getQuery()->getResult();
        /*** @var ClassifiedsModelType $modelType*/
        foreach ($modelTypes as $modelType) {
            $model = $modelType->getModel();
            $make = $model->getMake();
            $years = $modelType->getYears();
            asort($years);

            $this->outputInterface->writeln('<info>Start transaction</info>');
            $connection->beginTransaction();

            try {
                $this->outputInterface->writeln(sprintf('<comment>Crawling %s - %s - %s from %d -> %d</comment>', $make->getName(), $model->getName(), $modelType->getTrim(), min($years), max($years)));

                foreach ($years as $year) {
                    $href0 = ['href' => sprintf('https://api.manheim.com/valuations/id/%d%s%s%s?country=US&region=NA&include=retail,historical,forecast', $year, $make->getSourceId(), $model->getSourceId(), $modelType->getTrimSourceId())];
                    $href1 = ['href' => sprintf('https://api.manheim.com/valuation-samples/id/%d%s%s%s?country=US&orderBy=purchaseDate desc&start=1&limit=100', $year, $make->getSourceId(), $model->getSourceId(), $modelType->getTrimSourceId())];
                    $response = $this->getResponse(['requests' => [$href0, $href1]]);
                    $results = json_decode($response->getBody(), true);

                    if (!is_array($results)) {
                        //bounce
                        throw new \RuntimeException('No results!');
                    }

                    $ads = $results['responses'][1]['body']['items'];

                    foreach ($ads as $ad) {
                        $transmission = trim(strtolower($ad['vehicleDetails']['transmission']));
                        $bodyCondition = trim(strtolower($ad['vehicleDetails']['condition']));

                        if (s($transmission)->contains('speed') || s($transmission)->contains('manual')) {
                            $transmission = 'manual';
                        } else {
                            $transmission = 'automatic';
                        }

                        switch ($bodyCondition) {
                            case 'avg':
                                $bodyCondition = 'good';
                                break;
                            case 'above':
                                $bodyCondition = 'excellent';
                                break;
                            default:
                                $bodyCondition = 'fair';
                        }

                        $classifiedAd = new ClassifiedsAd($this->source);
                        $classifiedAd->setTransmission($transmission);
                        $classifiedAd->setCylinders(trim(intval(preg_replace('/[^0-9]+/', '', $ad['vehicleDetails']['engine']), 10)));
                        $classifiedAd->setExteriorColor(trim(strtolower($ad['vehicleDetails']['color'])));
                        $classifiedAd->setMileage(trim($ad['vehicleDetails']['odometer']));
                        $classifiedAd->setMileageSuffix(ClassifiedsAd::MILEAGE_MILES);
                        $classifiedAd->setBodyCondition($bodyCondition);
                        $classifiedAd->setSourceCreatedAt(new \DateTime(trim($ad['purchaseDate'])));
                        $classifiedAd->setPrice(trim($ad['pricePurchased']));
                        $classifiedAd->setCurrency(ClassifiedsAd::CURRENCY_USD);
                        $classifiedAd->setMake($make->getName());
                        $classifiedAd->setModel($model->getName());
                        $classifiedAd->setClassifiedsModel($model);
                        $classifiedAd->setModelType($modelType->getName());
                        $classifiedAd->setClassifiedsModelType($modelType);
                        $classifiedAd->setYear($year);

                        $this->entityManager->persist($classifiedAd);
                    }
                }

                $this->entityManager->flush();
                $this->outputInterface->writeln('<info>Commit transaction</info>');
                $connection->commit();
            } catch (\RuntimeException $e) {
                $this->outputInterface->writeln('<info>Rollback transaction</info>');
                $this->entityManager->getConnection()->rollback();
                $this->outputInterface->writeln(sprintf('<error>Reason: %s</error>', $e->getMessage()));
                exit(1);
            }
        }
    }

    protected function processMakesmodels()
    {
        $makesUrl = 'https://webservices.manheim.com/MMRDecoderWebService/decoders/makeList?year=%d&country=US';
        $modelsUrl = 'https://webservices.manheim.com/MMRDecoderWebService/decoders/modelList/make/%s?year=%d&country=US';
        $modelTypesUrl = 'https://webservices.manheim.com/MMRDecoderWebService/decoders/styleList/make/%s/model/%s/year/%d?country=US';
        $this->fetchMakes($makesUrl, '');
        $this->fetchModels($modelsUrl);
        $this->fetchModelTypes($modelTypesUrl);
    }

    protected function fetchMakes($url, $discoverer)
    {
        $this->outputInterface->writeln(sprintf('<info>Crawling MAKES from %s (%s)</info>', $this->siteName, $this->source));
        $this->outputInterface->writeln('<info>Start transaction</info>');
        $this->entityManager->getConnection()->beginTransaction();
        $total = 0;
        $years = array_reverse(range($this->yearTo, $this->yearFrom));

        try {
            foreach ($years as $year) {
                $response = $this->getResponse(['requests' => [['href' => sprintf($url, $year)]]]);

                $results = json_decode($response->getBody(), true);

                if (!is_array($results)) {
                    //bounce
                    throw new \RuntimeException('No results!');
                }

                $makes = $results['responses'][0]['body']['make'];

                foreach ($makes as $make) {
                    $existingMake = $this->entityManager->getRepository('WbcCrawlerBundle:ClassifiedsMake')
                        ->findOneBy(['source' => $this->source, 'sourceId' => $make['id']]);

                    if ($existingMake) {
                        continue;
                    }

                    $theMake = new ClassifiedsMake();
                    $theMake->setSource($this->source);
                    $theMake->setSourceId($make['id']);
                    $theMake->setName($make['name']);

                    $this->outputInterface->writeLn(sprintf('<info>Added: %s</info>', $theMake->getName()));

                    $this->entityManager->persist($theMake);
                    ++$total;
                }

                $this->entityManager->flush();
            }

            $this->outputInterface->writeLn(sprintf('<comment>%d ClassifiedsMakes added from: %s</comment>', $total, $this->source));

            $this->outputInterface->writeln('<info>Commit transaction</info>');
            $this->entityManager->getConnection()->commit();
        } catch (\RuntimeException $e) {
            $this->outputInterface->writeln('<info>Rollback transaction</info>');
            $this->entityManager->getConnection()->rollback();
            $this->outputInterface->writeln(sprintf('<error>Reason: %s</error>', $e->getMessage()));
            exit(1);
        }

        $this->outputInterface->writeln(sprintf('<info>Done Crawling MAKES from %s (%s)</info>', $this->siteName, $this->source));
    }

    protected function fetchModels($url)
    {
        $this->outputInterface->writeln(sprintf('<info>Crawling MODELS from %s (%s)</info>', $this->siteName, $this->source));
        $this->outputInterface->writeln('<info>Start transaction</info>');
        $this->entityManager->getConnection()->beginTransaction();
        $total = 0;

        try {
            $makes = $this->entityManager->getRepository('WbcCrawlerBundle:ClassifiedsMake')->findBy(['source' => $this->source]);
            $years = array_reverse(range($this->yearTo, $this->yearFrom));

            foreach ($makes as $make) {
                foreach ($years as $year) {
                    $response = $this->getResponse(['requests' => [['href' => sprintf($url, $make->getSourceId(), $year)]]]);
                    $results = json_decode($response->getBody(), true);

                    if (!is_array($results)) {
                        //bounce
                        throw new \RuntimeException('No results!');
                    }

                    $models = $results['responses'][0]['body']['model'];

                    foreach ($models as $model) {
                        $queryBuilder = $this->entityManager->createQueryBuilder()
                            ->select('model')
                            ->from('WbcCrawlerBundle:ClassifiedsModel', 'model')
                            ->innerJoin('model.make', 'make', 'WITH', 'make.source = :source')
                            ->where('model.sourceId = :sourceId')
                            ->setParameter(':source', $this->source)
                            ->setParameter(':sourceId', $model['id']);

                        $existingModel = $queryBuilder->getQuery()->getOneOrNullResult();

                        if ($existingModel) {
                            continue;
                        }

                        $theModel = new ClassifiedsModel();
                        $theModel->setName($model['name']);
                        $theModel->setSourceId($model['id']);
                        $theModel->setMake($make);

                        $this->entityManager->persist($theModel);
                        ++$total;
                    }

                    $this->entityManager->flush();
                    $this->outputInterface->writeLn(sprintf('<comment>ClassifiedsModels saved for ClassifiedsMake => %s and year => %d</comment>', $make->getName(), $year));
                }
            }

            $this->outputInterface->writeLn(sprintf('<comment>%d ClassifiedsModels added from: %s</comment>', $total, $this->source));
            $this->outputInterface->writeln('<info>Commit transaction</info>');
            $this->entityManager->getConnection()->commit();
        } catch (\RuntimeException $e) {
            $this->outputInterface->writeln('<info>Rollback transaction</info>');
            $this->entityManager->getConnection()->rollback();
            $this->outputInterface->writeln(sprintf('<error>Reason: %s</error>', $e->getMessage()));
            exit(1);
        }
    }

    protected function fetchModelTypes($url)
    {
        $this->outputInterface->writeln(sprintf('<info>Crawling MODEL TYPES from %s (%s)</info>', $this->siteName, $this->source));
        $this->outputInterface->writeln('<info>Start transaction</info>');
        $this->entityManager->getConnection()->beginTransaction();
        $total = 0;

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('model')
            ->from('WbcCrawlerBundle:ClassifiedsModel', 'model')
            ->innerJoin('model.make', 'make', 'WITH', 'make.source = :source')
            ->setParameter(':source', $this->source);

        $models = $queryBuilder->getQuery()->getResult();
        $years = array_reverse(range($this->yearTo, $this->yearFrom));

        try {
            foreach ($models as $model) {
                foreach ($years as $year) {
                    $make = $model->getMake();

                    $response = $this->getResponse(['requests' => [['href' => sprintf($url, $make->getSourceId(), $model->getSourceId(), $year)]]]);
                    $results = json_decode($response->getBody(), true);

                    if (!is_array($results)) {
                        //bounce
                        throw new \RuntimeException('No results!');
                    }

                    $modelTypes = $results['responses'][0]['body']['style'];

                    foreach ($modelTypes as $modelType) {
                        $queryBuilder = $this->entityManager->createQueryBuilder()
                            ->select('modelType')
                            ->from('WbcCrawlerBundle:ClassifiedsModelType', 'modelType')
                            ->innerJoin('modelType.model', 'model', 'WITH', 'modelType.model = model')
                            ->innerJoin('model.make', 'make', 'WITH', 'make.source = :source')
                            ->where('modelType.trimSourceId = :sourceId')
                            ->setParameter(':source', $this->source)
                            ->setParameter(':sourceId', $modelType['id']);

                        $existingModelType = $queryBuilder->getQuery()->getOneOrNullResult();

                        if ($existingModelType) {
                            $existingModelType->addYear($year);
                            continue;
                        }

                        $theModelType = new ClassifiedsModelType();
                        $theModelType->setModel($model);
                        $theModelType->setTrim($modelType['name']);
                        $theModelType->setTrimSourceId($modelType['id']);
                        $theModelType->setYears([$year]);

                        $this->entityManager->persist($theModelType);
                        ++$total;
                    }

                    $this->entityManager->flush();
                }

                $this->outputInterface->writeLn(sprintf('<comment>ClassifiedsModelTypes saved for ClassifiedsModel => %s</comment>', $model->getName()));
                $this->outputInterface->writeln('<info>Commit transaction</info>');
                $this->entityManager->getConnection()->commit();

                $this->outputInterface->writeln('<info>Start transaction</info>');
                $this->entityManager->getConnection()->beginTransaction();
            }
        } catch (\RuntimeException $e) {
            $this->outputInterface->writeln('<info>Rollback transaction</info>');
            $this->entityManager->getConnection()->rollback();
            $this->outputInterface->writeln(sprintf('<error>Reason: %s</error>', $e->getMessage()));
            exit(1);
        }
        $this->outputInterface->writeLn(sprintf('<comment>%d ClassifiedsModelTypes added from: %s</comment>', $total, $this->source));
    }

    private function getResponse(array $body)
    {
        return $this->guzzleClient->post($this->url, [
            'body' => json_encode($body),
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->getContainer()->getParameter('crawler_manheim_authorization'),
                'User-Agent' => $this->getContainer()->getParameter('crawler_user_agent'),
            ],
        ]);
    }
}
