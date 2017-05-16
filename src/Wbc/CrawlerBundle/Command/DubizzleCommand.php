<?php

namespace Wbc\CrawlerBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\DomCrawler\Crawler;
use Wbc\CrawlerBundle\Entity\ClassifiedsAd;
use Wbc\CrawlerBundle\Entity\ClassifiedsMake;
use Wbc\CrawlerBundle\Entity\ClassifiedsModel;
use Stringy\Stringy as s;
use Wbc\ValuationBundle\Entity\TrainingData;

/**
 * Class DubizzleCommand.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class DubizzleCommand extends ClassifiedsCommand
{
    protected $url = 'https://uae.dubizzle.com';
    protected $source = ClassifiedsAd::SOURCE_DUBIZZLE;
    protected $siteName = 'Dubizzle';
    protected $adsUrl = 'https://wd0ptz13zs-dsn.algolia.net/1/indexes/*/queries';

    protected function configure()
    {
        $this->setName('crawler:dubizzle:crawl')
            ->setDescription('Command to crawl Dubizzle; it can crawl Dubizzle Makes/Models and Dubizzle ADS')
            ->addArgument('type', InputArgument::REQUIRED, sprintf('Choose the type to crawl; "%s"', implode('","', $this->types)))
            ->addOption('overwrite', null, InputOption::VALUE_OPTIONAL, 'Either true or false; if false existing rows will be ignored');
    }

    protected function processAds()
    {
        $this->outputInterface->writeln(sprintf('<info>Crawling Ads from %s (%s)</info>', $this->siteName, $this->source));
        $currentPage = 0;
        $connection = $this->entityManager->getConnection();

        $criteria = new \Doctrine\Common\Collections\Criteria();
        $criteria->where($criteria->expr()->eq('source', $this->source));
        $criteria->andWhere($criteria->expr()->gte('name', 'Mazda'));
        $criteria->orderBy(['name' => 'ASC']);

        $makes = $this->entityManager->getRepository('WbcCrawlerBundle:ClassifiedsMake')->matching($criteria);

        foreach ($makes as $make) {
            $models = $make->getModels();

            $this->outputInterface->writeln('<info>Start transaction</info>');
            $connection->beginTransaction();

            try {
                /**@var ClassifiedsModel $model*/
                foreach ($models as $model) {
                    $_modelName = $model->getName();
                    $makeName = $make->getName();
                    $modelName = (string) s::create($_modelName)->replace('/', '');
                    $this->outputInterface->writeln(sprintf('<comment>Crawling %s - %s from %d -> %d</comment>', $makeName, $modelName, $this->yearFrom, $this->yearTo));

                    $this->doAlgolia($model, $makeName, $modelName);
                }
                $this->entityManager->flush();
                $this->outputInterface->writeln('<info>Commit transaction</info>');
                $connection->commit();
            } catch (\RuntimeException $e) {
                $this->outputInterface->writeln('<info>Rollback transaction</info>');
                $connection->rollback();
                $this->outputInterface->writeln(sprintf('<error>Stopped on Page: %d of (%s - %s) at: %s because of: %s</error>', $currentPage, $make->getName(), $model->getName(), (new \DateTime())->format('Y-m-d H:i:s'), $e->getMessage()));
                exit(1);
            }
        }
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

    private function doAlgolia(ClassifiedsModel $model, $makeName, $modelName)
    {
        $parameters = $this->getContainer()->getParameter('crawler_dubizzle_auth');
        $firstPage = 0;
        $perPage = 333;

        $formData = ['requests' => [['indexName' => 'motors.com', 'params' => 'query=&hitsPerPage='.$perPage.'&maxValuesPerFacet=1000&page='.$firstPage.'&filters=category_slug_tree.lvl3:\'motors > used-cars > '.strtolower($makeName).' > '.strtolower($modelName).'\' AND details.Year.en.value>2000 AND details.Year.en.value<2018&facets=["price","details.Year.en.value","details.Kilometers.en.value","details.Seller Type.en.value","details.Badges.en.value","has_vin","details.Motors Trim.en.value","places.en","details.Agent.en.value","details.Body Type.en.value","details.Engine Size.en.value","details.Color.en.value","details.Compatible With.en.value","details.Doors.en.value","details.No. Of Cylinders.en.value","details.Technical Features.en.value","details.Extras.en.value","details.Horsepower.en.value","added","details.Number of digits.en.value","details.Plate Code.en.value","details.Transmission Type.en.value","details.Warranty.en.value","details.Fuel Type.en.value","details.Regional Specs.en.value","language","has_photos","category_tree.en.lvl0","category_tree.en.lvl1","category_tree.en.lvl2"]&tagFilters=&facetFilters=[["category_tree.en.lvl2:Used Cars for Sale > '.$makeName.' > '.$modelName.'"]]&numericFilters=["details.Year.en.value>=2000","details.Year.en.value<=2018"]']]];
        $response = $this->guzzleClient->post($this->adsUrl, ['body' => json_encode($formData), 'headers' => ['content-type' => 'application/json'], 'query' => $parameters]);
        $results = json_decode($response->getBody(), true);
        if (!is_array($results)) {
            //bounce
            throw new \RuntimeException('No results!');
        }
        $pages = $results['results'][0]['nbPages'];
        for ($page = 0; $page < $pages; ++$page) {
            $formData = ['requests' => [['indexName' => 'motors.com', 'params' => 'query=&hitsPerPage='.$perPage.'&maxValuesPerFacet=1000&page='.$page.'&filters=category_slug_tree.lvl3:\'motors > used-cars > '.strtolower($makeName).' > '.strtolower($modelName).'\' AND details.Year.en.value>2000 AND details.Year.en.value<2018&facets=["price","details.Year.en.value","details.Kilometers.en.value","details.Seller Type.en.value","details.Badges.en.value","has_vin","details.Motors Trim.en.value","places.en","details.Agent.en.value","details.Body Type.en.value","details.Engine Size.en.value","details.Color.en.value","details.Compatible With.en.value","details.Doors.en.value","details.No. Of Cylinders.en.value","details.Technical Features.en.value","details.Extras.en.value","details.Horsepower.en.value","added","details.Number of digits.en.value","details.Plate Code.en.value","details.Transmission Type.en.value","details.Warranty.en.value","details.Fuel Type.en.value","details.Regional Specs.en.value","language","has_photos","category_tree.en.lvl0","category_tree.en.lvl1","category_tree.en.lvl2"]&tagFilters=&facetFilters=[["category_tree.en.lvl2:Used Cars for Sale > '.$makeName.' > '.$modelName.'"]]&numericFilters=["details.Year.en.value>=2000","details.Year.en.value<=2018"]']]];
            $response = $this->guzzleClient->post($this->adsUrl, ['body' => json_encode($formData), 'headers' => ['content-type' => 'application/json'], 'query' => $parameters]);
            $results = json_decode($response->getBody(), true);
            if (!is_array($results)) {
                //bounce
                throw new \RuntimeException('No results!');
            }
            $hits = $results['results'][0]['hits'];
            foreach ($hits as $ad) {
                $classifiedAd = new ClassifiedsAd($this->source);
                $classifiedAd->setTitle($ad['name']['en']);

                if (isset($ad['details']['Make']['en']['value'])) {
                    $classifiedAd->setMake($ad['details']['Make']['en']['value']);
                }

                if (isset($ad['details']['Model']['en']['value'])) {
                    $classifiedAd->setModel($ad['details']['Model']['en']['value']);
                }

                if (isset($ad['details']['Motors Trim']['en']['value'])) {
                    $classifiedAd->setTrim($ad['details']['Motors Trim']['en']['value']);
                }

                if (isset($ad['details']['Year']['en']['value'])) {
                    $classifiedAd->setYear($ad['details']['Year']['en']['value']);
                }

                if (isset($ad['details']['No. Of Cylinders']['en']['value'])) {
                    $classifiedAd->setCylinders($ad['details']['No. Of Cylinders']['en']['value']);
                }

                if (isset($ad['details']['Color']['en']['value'])) {
                    $classifiedAd->setExteriorColor($ad['details']['Color']['en']['value']);
                }

                if (isset($ad['details']['Kilometers']['en']['value'])) {
                    $classifiedAd->setMileage($ad['details']['Kilometers']['en']['value']);
                }

                if (isset($ad['details']['Body Type']['en']['value'])) {
                    $classifiedAd->setBodyType($ad['details']['Body Type']['en']['value']);
                }

                if (isset($ad['details']['Doors']['en']['value'])) {
                    $classifiedAd->setDoors(intval(str_replace([' door', '+ door', '+ doors', '+', ' doors'], '', $ad['details']['Doors']['en']['value'])));
                }

                if (isset($ad['details']['Regional Specs']['en']['value'])) {
                    $classifiedAd->setSpecifications(str_replace([' Specs', ' Specifications'], '', $ad['details']['Regional Specs']['en']['value']));
                }

                if (isset($ad['condition'])) {
                    $classifiedAd->setIsUsed(boolval(!$ad['condition']));
                }

                if (isset($ad['details']['Body Condition']['en']['value'])) {
                    $classifiedAd->setBodyCondition($ad['details']['Body Condition']['en']['value']);
                }

                if (isset($ad['details']['Mechanical Condition']['en']['value'])) {
                    $classifiedAd->setMechanicalCondition($ad['details']['Mechanical Condition']['en']['value']);
                }

                if (isset($ad['details']['Horsepower']['en']['value'])) {
                    $classifiedAd->setHorsepower($ad['details']['Horsepower']['en']['value']);
                }

                if (isset($ad['details']['Transmission Type']['en']['value'])) {
                    $classifiedAd->setTransmission(strtolower(str_replace([' Transmission'], '', $ad['details']['Transmission Type']['en']['value'])));
                }

                if (isset($ad['price'])) {
                    $classifiedAd->setPrice($ad['price']);
                }

                $classifiedAd->setClassifiedsModel($model);
                $classifiedAd->setModel($modelName);
                if (isset($ad['site']['en'])) {
                    $classifiedAd->setCity($ad['site']['en']);
                }

                if (isset($ad['id'])) {
                    $classifiedAd->setSourceId($ad['id']);
                }

                if (isset($ad['absolute_url']['en'])) {
                    $classifiedAd->setUrl($ad['absolute_url']['en']);
                }

                if ($ad['has_photos'] == 1 && isset($ad['photos'])) {
                    $classifiedAd->addImageUrl($ad['photos']['main']);
                }

                if (isset($ad['details']['Engine Size']['en']['value'])) {
                    $classifiedAd->setEngineSize($ad['details']['Engine Size']['en']['value']);
                }

                if ($ad['seller_type'] == 'DL' && isset($ad['details']['Agent']['en']['value'])) {
                    $classifiedAd->setDealerName($ad['details']['Agent']['en']['value']);
                }

                $classifiedAd->setSourceCreatedAt((new \DateTime())->setTimestamp($ad['added']));

                $this->entityManager->persist($classifiedAd);
            }
        }
    }

    /**
     * @fixme: Doesn't work. Might not be needed as Dubizzle changed their UI.
     *
     * @param $model
     * @param $makeName
     * @param $modelName
     */
    private function doHtml($model, $makeName, $modelName)
    {
        $page = 1;
        // https://uae.dubizzle.com/motors/used-cars/nissan/sunny/?page=1&year__gte=2000&year__lte=2018&is_basic_search_widget=0&is_search=1

        do {
            $url = sprintf('%s/motors/used-cars/%s/%s', $this->url, s::create($makeName)->slugify()->toLowerCase(), s::create($modelName)->slugify()->toLowerCase());
            $query = [
                'page' => $page,
                'year__gte' => $this->yearFrom,
                'year__lte' => $this->yearTo,
                'is_basic_search_widget' => 0,
                'is_search' => 1,
            ];

            $response = $this->guzzleClient->get($url, ['query' => $query]);
            $listCrawler = new Crawler((string) $response->getBody());
            $featuredUrls = $listCrawler->filter('#featured-content h3.featured-ad-title a')->extract(['href']);
            $listUrls = $listCrawler->filter('#results-list span.title a')->extract(['href']);
            $listUrls = array_merge($featuredUrls, $listUrls);

            foreach ($listUrls as $listUrl) {
                $response = $this->guzzleClient->get($url);
                $detailCrawler = new Crawler((string) $response->getBody());

                $classifiedAd = new ClassifiedsAd($this->source);
                $classifiedAd->setTitle(trim($detailCrawler->filter('#listing-title-wrap')->text()));
                $classifiedAd->setMake(trim($detailCrawler->filter('#c1:swfield option[selected="selected"]')->text()));
                $classifiedAd->setModel(trim($detailCrawler->filter('#c2:swfield option[selected="selected"]')->text()));

                $detailCrawler->filter('.normal-field-labels')->each(function (Crawler $node, $i) {
                    //Body Condition, Mechanical Condition, Trim, No. Of Cylinders, Horsepower
                });

                $classifiedAd->setTrim($detailCrawler->filter('.normal-field-labels:contains("Trim")')->nextAll('.normal-field-strong')->text());
                $classifiedAd->setYear($ad['details']['Year']['en']['value']);
                $classifiedAd->setCylinders($ad['details']['No. Of Cylinders']['en']['value']);
                $classifiedAd->setExteriorColor($ad['details']['Color']['en']['value']);
                $classifiedAd->setMileage($ad['details']['Kilometers']['en']['value']);
                $classifiedAd->setBodyType($ad['details']['Body Type']['en']['value']);
                $classifiedAd->setDoors(intval(str_replace([' door', '+ door', '+ doors', '+', ' doors'], '', $ad['details']['Doors']['en']['value'])));
                $classifiedAd->setSpecifications($ad['details']['Regional Specs']['en']['value']);
                $classifiedAd->setIsUsed(!$ad['condition']);
                $classifiedAd->setBodyCondition($ad['details']['Body Condition']['en']['value']);
                $classifiedAd->setMechanicalCondition($ad['details']['Mechanical Condition']['en']['value']);
                $classifiedAd->setHorsepower($ad['details']['Horsepower']['en']['value']);
                $classifiedAd->setTransmission(strtolower(str_replace([' Transmission'], '', $ad['details']['Transmission Type']['en']['value'])));
                $classifiedAd->setPrice($ad['price']);
                $classifiedAd->setClassifiedsModel($model);
                $classifiedAd->setCity($ad['site']['en']);
                $classifiedAd->setSourceId($ad['id']);
                $classifiedAd->setUrl($ad['absolute_url']['en']);
                if ($ad['has_photos'] == 1 && isset($ad['photos'])) {
                    $classifiedAd->addImageUrl($ad['photos']['main']);
                }
                if (isset($ad['details']['Engine Size']['en']['value'])) {
                    $classifiedAd->setEngineSize($ad['details']['Engine Size']['en']['value']);
                }
                $classifiedAd->setSourceCreatedAt((new \DateTime())->setTimestamp($ad['added']));
                if ($ad['seller_type'] == 'DL') {
                    $classifiedAd->setDealerName($ad['details']['Agent']['en']['value']);
                }
                $this->entityManager->persist($classifiedAd);
            }

            ++$page;
        } while ($listCrawler->filter('#last_page')->count() >= 1);
    }
}
