<?php

namespace Wbc\CrawlerBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\DomCrawler\Crawler;
use Wbc\CrawlerBundle\Entity\ClassifiedsMake;
use Wbc\CrawlerBundle\Entity\ClassifiedsModel;

/**
 * Class DubizzleCommand.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class DubizzleCommand extends ClassifiedsCommand
{
    protected $url = 'https://uae.dubizzle.com';
    protected $source = 'dubizzle.com';
    protected $siteName = 'Dubizzle';

    protected function configure()
    {
        $this->setName('crawler:dubizzle:crawl')
            ->setDescription('Command to crawl Dubizzle; it can crawl Dubizzle Makes/Models and Dubizzle ADS')
            ->addArgument('type', InputArgument::REQUIRED, sprintf('Choose the type to crawl; "%s"', implode('","', $this->types)))
            ->addOption('overwrite', null, InputOption::VALUE_OPTIONAL, 'Either true or false; if false existing rows will be ignored');
    }

    protected function processMakesmodels()
    {
        parent::processMakesmodels();
        $this->fetchMakes('/motors/used-cars/', "//select[@id='c1:swfield']//option");
        $this->fetchModels('/classified/get_category_models/%d/?site=--&s=MT', '//body');
    }

    protected function fetchModels($url, $discoverer)
    {
        $this->outputInterface->writeln(sprintf('<info>Crawling MODELS from %s (%s)</info>', $this->siteName, $this->source));

        $this->outputInterface->writeln('<info>Start transaction</info>');
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $makes = $this->entityManager->getRepository('WbcCrawlerBundle:ClassifiedsMake')->findBy(array('source' => $this->source));
            foreach ($makes as $make) {
                $html = (string) $this->guzzleClient->get(sprintf($url, $make->getSourceId()))->getBody();

                $crawler = new Crawler($html);
                $crawler = $crawler->filterXpath($discoverer);

                foreach ($crawler as $domElement) {
                    $modelObjects = json_decode($domElement->nodeValue);

                    if (property_exists($modelObjects, 'models')) {
                        if (is_array($modelObjects->models)) {
                            foreach ($modelObjects->models as $modelObject) {
                                $value = trim($modelObject[0]);
                                $text = trim($modelObject[1]);

                                if ($value && $value != '--') {
                                    $model = $this->entityManager->getRepository('WbcCrawlerBundle:ClassifiedsModel')
                                        ->findOneBy(array('make' => $make, 'sourceId' => $value));

                                    if (!$model) {
                                        $model = new ClassifiedsModel();
                                        $model->setSourceId($value);
                                        $model->setName($text);
                                        $model->setMake($make);
                                        $this->entityManager->persist($model);
                                    }

                                    if ($this->overwrite) {
                                        $model->setName($text);
                                    }

                                    $this->entityManager->flush();
                                    $this->outputInterface->writeLn(sprintf('<comment>ClassifiedsModel with ID: %d saved -> %s [%s] for ClassifiedsMake: %s</comment>', $model->getId(), $text, $value, $make->getName()));
                                }
                            }
                        }
                    }
                }
            }

            $this->outputInterface->writeln('<info>Commit transaction</info>');
            $this->entityManager->getConnection()->commit();
        } catch (\RuntimeException $e) {
            $this->outputInterface->writeln('<info>Rollback transaction</info>');
            $this->entityManager->getConnection()->rollback();
            $this->outputInterface->writeln(sprintf('<error>Reason: %s</error>', $e->getMessage()));
        }

        $this->outputInterface->writeln(sprintf('<info>Done Crawling MODELS from %s (%s)</info>', $this->siteName, $this->source));
    }
}
