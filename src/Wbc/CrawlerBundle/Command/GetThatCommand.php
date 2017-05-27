<?php

namespace Wbc\CrawlerBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Wbc\CrawlerBundle\Entity\ClassifiedsAd;
use Wbc\CrawlerBundle\Entity\ClassifiedsModel;
use Stringy\Stringy as s;

/**
 * Class GetThatCommand.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class GetThatCommand extends ClassifiedsCommand
{
    protected $url = 'https://getthat.com/autos';
    protected $source = ClassifiedsAd::SOURCE_GETTHAT;
    protected $siteName = 'getthat';
    protected $adsUrl = 'https://tfa301y859-dsn.algolia.net/1/indexes/*/queries';
    protected $makes = ['Alfa Romeo', 'Audi', 'BMW', 'Bentley', 'Cadillac', 'Chevrolet', 'Chrysler', 'Daewoo', 'Daihatsu', 'Dodge', 'Ferrari', 'Ford', 'GMC', 'Great Wall', 'Honda', 'Hummer', 'Hyundai', 'Infiniti', 'Isuzu', 'Jaguar', 'Jeep', 'Kia', 'Land Rover', 'Lexus', 'Lincoln', 'Luxgen', 'MG', 'Mazda', 'Mercedes-Benz', 'Mini', 'Mitsubishi', 'Nissan', 'Peugeot', 'Pontiac', 'Porsche', 'Range Rover', 'Renault', 'Saab', 'Subaru', 'Suzuki', 'TATA', 'Toyota', 'Volkswagen', 'Volvo', 'ZX Auto'];

    protected function configure()
    {
        $this->setName('crawler:getthat:crawl')
            ->setDescription('Command to crawl getthat.com; it crawls getthat.com Makes/Models and getthat.com ADS')
            ->addArgument('type', InputArgument::REQUIRED, sprintf('Choose the type to crawl; "%s"', implode('","', $this->types)))
            ->addOption('overwrite', null, InputOption::VALUE_OPTIONAL, 'Either true or false; if false existing rows will be ignored');
    }

    protected function processAds()
    {
        $page = 0;
        $perPage = 1000;
        $parameters = $this->getContainer()->getParameter('crawler_getthat_auth');

        foreach ($this->makes as $make) {
            $formData = [
                'requests' => [
                    [
                        'indexName' => 'prd_autos_adv_search',
                        'params' => 'query=&page='.$page.'&hitsPerPage='.$perPage.'&facets=["price","mileage","fuelType","transmission","sellerType","carType","badges.name","amenities","doors","warranty","companyName","make","model","color","makeYearAsText","bodyType","city","makeYear","location"]&tagFilters=&facetFilters=[["make:'.$make.'"]]',
                    ],
                ],
            ];

            $this->outputInterface->writeln(sprintf('<info>Crawling Ads from %s (%s)</info>', $this->siteName, $this->source));

            $response = $this->guzzleClient->post($this->adsUrl, [
                'body' => json_encode($formData),
                'headers' => ['content-type' => 'application/json'],
                'query' => $parameters,
            ]);

            try {
                $results = json_decode($response->getBody(), true);

                if (!is_array($results)) {
                    //bounce
                    throw new \RuntimeException('No results!');
                }

                $hits = $results['results'][0]['hits'];

                foreach ($hits as $ad) {
                    $classifiedAd = new ClassifiedsAd($this->source);
                    $classifiedAd->setTitle($ad['title']);
                    $classifiedAd->setMake($ad['make']);
                    $classifiedAd->setModel($ad['model']);
                    $classifiedAd->setModelType($ad['trim']);
                    $classifiedAd->setYear($ad['makeYear']);

                    if (!empty($ad['cylinders']) && is_numeric($ad['cylinders'])) {
                        $classifiedAd->setCylinders($ad['cylinders']);
                    }

                    $classifiedAd->setExteriorColor(!empty($ad['color']) ? $ad['color'] : null);
                    $classifiedAd->setMileage($ad['mileage']);
                    $classifiedAd->setBodyType($ad['bodyType']);
                    $classifiedAd->setDoors(!empty($ad['doors']) ? intval((string) s::create($ad['doors'])->replace('+', '')) : null);
                    $classifiedAd->setSpecifications($ad['regionalSpecs']);
                    $classifiedAd->setIsUsed($ad['carType'] == 'used' ? true : false);
                    $classifiedAd->setBodyCondition(!empty($ad['bodyCondition']) ? $ad['bodyCondition'] : null);
                    $classifiedAd->setMechanicalCondition(!empty($ad['mechanicalCondition']) ? $ad['mechanicalCondition'] : null);
                    $classifiedAd->setHorsepower(!empty($ad['horsepower']) ? $ad['horsepower'] : null);
                    $classifiedAd->setEngineSize(s::create($ad['engineSize'])->replace('L', ''));
                    $classifiedAd->setTransmission(!empty($ad['transmission']) ? s::create($ad['transmission'])->toLowerCase() : null);
                    $classifiedAd->setPrice($ad['price']);

                    if (!empty($ad['city'])) {
                        $classifiedAd->setCity($ad['city']);
                    }

                    $classifiedAd->setDealerName(!empty($ad['companyName']) ? $ad['companyName'] : null);
                    $classifiedAd->setSourceId($ad['objectID']);
                    $classifiedAd->setSourceCreatedAt(!empty($ad['createdOn']) ? new \DateTime($ad['createdOn']) : null);
                    $classifiedAd->setSourceUpdatedAt(!empty($ad['lastUpdated']) ? new \DateTime($ad['lastUpdated']) : null);

                    foreach ($ad['images'] as $image) {
                        if (!empty($image['imageProcessed']) && $image['imageProcessed'] == 'yes') {
                            $classifiedAd->addImageUrl($image['url']);
                        }
                    }

                    $this->entityManager->persist($classifiedAd);
                }
            } catch (\Exception $e) {
                $this->outputInterface->writeln(sprintf('<error>Stopped on Page: %d, at: %s because of: %s</error>', $page, (new \DateTime())->format('Y-m-d H:i:s'), $e->getMessage()));
                break;
            }

            $this->entityManager->flush();
            $this->outputInterface->writeln(sprintf('<info>Make: %s, Saved %d items!</info>', $make, $results['results'][0]['nbHits']));
        }
    }

    protected function fetchModels($url, $discoverer)
    {
        $this->outputInterface->writeln(sprintf('<info>Crawling MODELS from %s (%s)</info>', $this->siteName, $this->source));

        $this->outputInterface->writeln('<info>Start transaction</info>');
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $makes = $this->entityManager->getRepository('WbcCrawlerBundle:ClassifiedsMake')->findBy(array('source' => $this->source));
            foreach ($makes as $make) {
                $html = (string) $this->guzzleClient->post($url, ['form_params' => ['make' => $make->getSourceId()]])->getBody();

                $crawler = new Crawler($html);
                $crawler = $crawler->filterXpath($discoverer);

                foreach ($crawler as $domElement) {
                    $value = trim($domElement->getAttribute('value'));
                    $text = trim($domElement->textContent);

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
        } catch (\RuntimeException $e) {
            $this->outputInterface->writeln('<info>Rollback transaction</info>');
            $this->entityManager->getConnection()->rollback();
            $this->outputInterface->writeln(sprintf('<error>Reason: %s</error>', $e->getMessage()));
        }

        $this->outputInterface->writeln(sprintf('<info>Done Crawling MODELS from %s (%s)</info>', $this->siteName, $this->source));
    }
}
