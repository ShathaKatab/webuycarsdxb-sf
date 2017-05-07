<?php

namespace Wbc\CrawlerBundle\Command;

use Wbc\CrawlerBundle\Entity\ClassifiedsMake;
use Wbc\CrawlerBundle\Entity\ClassifiedsModel;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class TaqyeemCommand.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class TaqyeemCommand extends BaseCommand
{
    const REGION_GCC = '01';
    const REGION_NON_GCC = '06';

    protected $url = 'http://www.taqyeem.ae/old_web/getAPIs.php';
    protected $source = 'taqyeem.ae';
    private $years = array();

    protected function configure()
    {
        $this->setName('vehicle:taqyeem:crawl')
            ->setDescription('Command to fetch makes, models, body types from Taqyeem')
            ->addOption('overwrite', null, InputOption::VALUE_OPTIONAL, 'Either true or false; if false existing rows will be ignored');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->outputInterface->writeln('<info>Crawling makes, models, body types from Taqyeem (taqyeem.ae)</info>');

        foreach (array_keys($this->getRegions()) as $region) {

            $this->setYears($region);

            //start from latest going backwards
            foreach (array_reverse($this->years) as $year) {
                $this->fetchMakes($region, $year);
                $this->fetchModels($region, $year);
            }
        }
    }

    protected function setYears($region)
    {
        $html = (string)$this->guzzleClient->post($this->url, ['form_params' => ['region' => $region]])->getBody();

        $crawler = new Crawler($html);
        $crawler = $crawler->filterXPath('//option');
        $years = array();

        foreach ($crawler as $domElement) {
            if ($domElement->getAttribute('value')) {
                $years[] = $domElement->getAttribute('value');
                $this->outputInterface->writeLn(sprintf('<comment>Fetched year: %s</comment>', $domElement->getAttribute('value')));
            }
        }

        $this->years = $years;

        return $years;
    }

    protected function fetchMakes($region, $year)
    {
        $this->outputInterface->writeln(sprintf('<info>Processing Makes for "%s", "%s"</info>', $this->getRegionTitle($region), $year));

        $this->outputInterface->writeln('<info>Start transaction</info>');
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $request = $this->guzzleClient->post('', ['form_params' => ['region' => $region, 'year' => $year]]);

            $html = (string) $request->getBody();

            $crawler = new Crawler($html);
            $crawler = $crawler->filterXPath('//option');

            foreach ($crawler as $domElement) {
                $value = $domElement->getAttribute('value');
                $text  = $domElement->textContent;

                if ($value) {
                    $make = $this->entityManager->getRepository('WbcCrawlerBundle:ClassifiedsMake')
                        ->findOneBy(array('source' => $this->source, 'sourceId' => $value));

                    if ($make) {
                        if (!$this->overwrite) {
                            $this->outputInterface->writeLn(sprintf('<comment>-> %s [%s] exists!</comment>', $text, $value));
                            continue;
                        }
                    } else {
                        $make = new ClassifiedsMake();
                    }

                    $make->setName($text);
                    $make->setSource($this->source);
                    $make->setSourceId($value);
                    $this->entityManager->persist($make);

                    $this->entityManager->flush();

                    $this->outputInterface->writeLn(sprintf('<comment>ClassifiedsMake with ID: %d saved -> %s [%s]</comment>', $make->getId(), $text, $value));
                }
            }

            $this->outputInterface->writeln('<info>Commit transaction</info>');
            $this->entityManager->getConnection()->commit();
        } catch (\RuntimeException $e) {
            $this->outputInterface->writeln('<info>Rollback transaction</info>');
            $this->entityManager->getConnection()->rollback();
            $this->outputInterface->writeln(sprintf('<error>Reason: %s</error>', $e->getMessage()));
        }
    }

    protected function fetchModels($region, $year)
    {
        $makes = $this->entityManager->getRepository('WbcCrawlerBundle:ClassifiedsMake')->findBySource($this->source);
        $this->outputInterface->writeln('<info>Start transaction</info>');
        $this->entityManager->getConnection()->beginTransaction();
        try {
            foreach ($makes as $make) {
                $this->outputInterface->writeln(sprintf('<info>Processing Models for "%s", "%s", "%s"</info>',
                    $this->getRegionTitle($region), $year, $make->getName()));

                $request = $this->guzzleClient->post('', ['form_params' => ['region' => $region, 'year' => $year, 'make' => $make->getSourceId()]]);

                $html = (string) $request->getBody();

                $crawler = new Crawler($html);
                $crawler = $crawler->filterXPath('//option');

                foreach ($crawler as $domElement) {
                    $value = $domElement->getAttribute('value');
                    $text = $domElement->textContent;

                    if ($value) {
                        $model = $this->entityManager->getRepository('WbcCrawlerBundle:ClassifiedsModel')
                            ->findOneBy(array('make' => $make, 'sourceId' => $value));

                        if ($model) {
                            if (!$this->overwrite) {
                                $this->outputInterface->writeLn(sprintf('<comment>-> %s [%s] exists!</comment>', $text, $value));
                                $this->fetchTrims($region, $year, $model);
                                continue;
                            }
                        } else {
                            $model = new ClassifiedsModel();
                            $model->setMake($make);
                        }

                        $model->setSourceId($value);
                        $model->setName($text);

                        $this->entityManager->persist($model);
                        $this->entityManager->flush();

                        $this->outputInterface->writeLn(sprintf('<comment>ClassifiedsModel with ID: %d saved -> %s [%s]</comment>', $model->getId(), $text, $value));

                        $this->fetchTrims($region, $year, $model);
                    }
                }
            }
            $this->outputInterface->writeln('<info>Commit transaction</info>');
            $this->entityManager->getConnection()->commit();
        } catch (\RuntimeException $e) {
            $this->outputInterface->writeln('<info>Rollback transaction</info>');
            $this->entityManager->getConnection()->rollback();
            $this->outputInterface->writeln(sprintf('<info>Reason: %s</info>', $e->getMessage()));
        }
    }

    protected function fetchTrims($region, $year, ClassifiedsModel $model)
    {
        $make = $model->getMake();
        $this->outputInterface->writeln(sprintf('<info>Processing Trims for "%s", "%s", "%s-%s"</info>',
            $this->getRegionTitle($region), $year, $make->getName(), $model->getName()));

        $request = $this->guzzleClient->post('', ['form_params' => ['region' => $region, 'year' => $year, 'make' => $make->getSourceId(), 'model' => $model->getSourceId()]]);
        $html = (string) $request->getBody();

        $crawler = new Crawler($html);
        $crawler = $crawler->filterXPath('//option');

        foreach ($crawler as $domElement) {
            $value = $domElement->getAttribute('value');
            $text = $domElement->textContent;

            if ($value) {
                $this->fetchEngines($region, $year, $model, $value, $text);
            }
        }
    }

    protected function fetchEngines($region, $year, ClassifiedsModel $model, $trimSourceId, $trimName)
    {
        $make = $model->getMake();
        $this->outputInterface->writeln(sprintf('<info>Processing Engines for "%s", "%s", "%s-%s-%s"</info>',
            $this->getRegionTitle($region), $year, $make->getName(), $model->getName(), $trimName));

        $request = $this->guzzleClient->post('', ['form_params' => ['region' => $region, 'year' => $year, 'make' => $make->getSourceId(), 'model' => $model->getSourceId(), 'trim' => $trimSourceId]]);
        $html = (string) $request->getBody();

        $crawler = new Crawler($html);
        $crawler = $crawler->filterXPath('//option');

        foreach ($crawler as $domElement) {
            $value = $domElement->getAttribute('value');

            if ($value) {
                $this->fetchTransmissions($region, $year, $model, $trimSourceId, $trimName, $value);
            }
        }
    }

    protected function fetchTransmissions($region, $year, ClassifiedsModel $model, $trimSourceId, $trimName, $engine)
    {
        $make = $model->getMake();
        $this->outputInterface->writeln(sprintf('<info>Processing Transmissions for "%s", "%s", "%s-%s-%s-%s"</info>',
            $this->getRegionTitle($region), $year, $make->getName(), $model->getName(), $trimName, $engine));

        $request = $this->guzzleClient->post('', ['form_params' => ['region' => $region, 'year' => $year, 'make' => $make->getSourceId(),
            'model' => $model->getSourceId(), 'trim' => $trimSourceId, 'engine' => $engine, ]]);

        $html = (string) $request->getBody();

        $crawler = new Crawler($html);
        $crawler = $crawler->filterXPath('//option');

        foreach ($crawler as $domElement) {
            $value = $domElement->getAttribute('value');
            $text = $domElement->textContent;

            if ($value) {
                $this->fetchBodyTypes($region, $year, $model, $trimSourceId, $trimName, $engine, $value, $text);
            }
        }
    }

    protected function fetchBodyTypes($region, $year, ClassifiedsModel $model, $trimSourceId, $trimName, $engine, $transmissionId, $transmissionName)
    {
        $make = $model->getMake();
        $this->outputInterface->writeln(sprintf('<info>Processing BodyTypes for "%s", "%s", "%s-%s-%s-%s-%s"</info>',
            $this->getRegionTitle($region), $year, $make->getName(), $model->getName(), $trimName, $engine, $transmissionName));

        $request = $this->guzzleClient->post('', ['form_params' => ['region' => $region, 'year' => $year, 'make' => $make->getSourceId(),
            'model' => $model->getSourceId(), 'trim' => $trimSourceId, 'engine' => $engine, 'trans' => $transmissionId,]]);

        $html = (string) $request->getBody();

        $crawler = new Crawler($html);
        $crawler = $crawler->filterXPath('//option');

        //convert from liters to cc
        $engine = $engine * 1000;

        foreach ($crawler as $domElement) {
            $value = $domElement->getAttribute('value');
            $text = $domElement->textContent;
            $isGcc = $region == self::REGION_GCC ? true : false;

            if ($value) {
                $modelType = $this->entityManager->getRepository('WbcVehicleBundle:ModelType')
                    ->findOneBy(array(
                        'isGcc' => $isGcc,
                        'model' => $model,
                        'trimSourceId' => $trimSourceId,
                        'engine' => $engine,
                        'transmissionSourceId' => $transmissionId,
                        'bodyTypeSourceId' => $value,
                    ));

                if ($modelType) {
                    $this->outputInterface->writeLn(sprintf('<comment>->ModelType %s [%s] exists! Will update details!</comment>', $text, $value));
                } else {
                    $modelType = new ModelType();
                    $modelType->setModel($model);
                }

                $years = $modelType->getYears() ? array_merge($modelType->getYears(), array($year)) : array($year);
                $modelType->setYears($years);
                $modelType->setBodyTypeSourceId($value);
                $modelType->setBodyType($text);
                $modelType->setTrimSourceId($trimSourceId);
                $modelType->setTrim($trimName);
                $modelType->setEngine($engine);
                $modelType->setTransmissionSourceId($transmissionId);
                $modelType->setTransmission($transmissionName);
                $modelType->setIsGcc($isGcc);

                $this->entityManager->persist($modelType);
                $this->entityManager->flush();

                $this->outputInterface->writeLn(sprintf('<comment>ModelType with ID: %d saved -> %s [%s]</comment>', $modelType->getId(), $text, $value));
            }
        }
    }

    private function getRegions()
    {
        return array(
            self::REGION_GCC => 'GCC',
            self::REGION_NON_GCC => 'NON-GCC',
        );
    }

    private function getRegionTitle($region)
    {
        $regions = $this->getRegions();

        if (isset($regions[$region])) {
            return $regions[$region];
        }

        return '';
    }
}
