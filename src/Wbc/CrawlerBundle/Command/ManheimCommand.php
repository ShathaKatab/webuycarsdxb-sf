<?php

namespace Wbc\CrawlerBundle\Command;

use Wbc\CrawlerBundle\Entity\ClassifiedsAd;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Wbc\CrawlerBundle\Entity\ClassifiedsModel;

/**
 * Class ManheimCommand.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ManheimCommand extends ClassifiedsCommand
{
    protected $url = 'https://uae.dubizzle.com';
    protected $source = ClassifiedsAd::SOURCE_MANHEIM;
    protected $siteName = 'Manheim';

    protected function configure()
    {
        $this->setName('crawler:manheim:crawl')
            ->setDescription('Command to crawl manheim.com; it crawls manheim.com Models and manheim.com ADS')
            ->addArgument('type', InputArgument::REQUIRED, sprintf('Choose the type to crawl; "%s"', implode('","', $this->types)))
            ->addOption('overwrite', null, InputOption::VALUE_OPTIONAL, 'Either true or false; if false existing rows will be ignored');
    }

    protected function processMakesmodels()
    {
        //fetch models only
        $this->outputInterface->writeln(sprintf('<info>Crawling MODELS from %s (%s)</info>', $this->siteName, $this->source));
        $this->outputInterface->writeln('<info>Start transaction</info>');
        $this->entityManager->getConnection()->beginTransaction();

        $url = 'https://www.manheim.com/members/powersearch/getModelsVehicleTypes.do';
        $cookie = $this->getContainer()->getParameter('crawler_manheim_cookie');
        $userAgent = $this->getContainer()->getParameter('crawler_user_agent');

        try {
            $makes = $this->entityManager->getRepository('WbcCrawlerBundle:ClassifiedsMake')->findBy(['source' => $this->source]);

            foreach ($makes as $make) {
                $response = $this->guzzleClient->post($url, [
                    'body' => sprintf('make=%d&vehicleType=104000001&vehicleType=104000002&vehicleType=104000003&vehicleType=104000004', $make->getSourceId()),
                    'headers' => [
                        'Cookie' => $cookie,
                        'User-Agent' => $userAgent,
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                ]);

                $results = json_decode($response->getBody(), true);

                if (!is_array($results)) {
                    //bounce
                    throw new \RuntimeException('No results!');
                }

                foreach ($results['models'] as $result) {
                    if ($result['label'] == 'ALL' || $result['value'] == 'ALL') {
                        continue;
                    }

                    $classifiedsModel = new ClassifiedsModel();
                    $classifiedsModel->setName($result['label']);
                    $classifiedsModel->setSourceId($result['value']);
                    $classifiedsModel->setMake($make);
                    $this->entityManager->persist($classifiedsModel);
                }

                $this->entityManager->flush();
                $this->outputInterface->writeLn(sprintf('<comment>ClassifiedsModels for ClassifiedsMake: %s</comment>', $make->getName()));
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
