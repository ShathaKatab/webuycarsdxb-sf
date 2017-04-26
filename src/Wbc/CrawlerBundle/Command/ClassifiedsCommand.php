<?php

namespace Wbc\CrawlerBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Wbc\CrawlerBundle\Entity\ClassifiedsMake;

/**
 * Class ClassifiedsCommand.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
abstract class ClassifiedsCommand extends BaseCommand
{
    protected $types = array('makes-models', 'ads');
    protected $siteName = '';

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $type = $input->getArgument('type');

        if (!in_array($type, $this->types)) {
            throw new \RuntimeException(sprintf('Invalid type, valid types are: "%s"', implode('","', $this->types)));
        }

        $type = str_replace('-', '', $type);
        $methodName = 'process'.ucfirst($type);

        if (!method_exists($this, $methodName)) {
            throw new \RuntimeException(sprintf('Method "%s" doesn\'t exist!', $methodName));
        }

        call_user_func(array($this, $methodName));
    }

    protected function processMakesmodels()
    {
    } //implemented by child

    protected function fetchMakes($url, $discoverer)
    {
        $this->outputInterface->writeln(sprintf('<info>Crawling MAKES from %s (%s)</info>', $this->siteName, $this->source));

        $this->outputInterface->writeln('<info>Start transaction</info>');
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $html = (string) $this->guzzleClient->get($url)->getBody();

            $crawler = new Crawler($html);
            $crawler = $crawler->filterXPath($discoverer);

            foreach ($crawler as $domElement) {
                $value = trim($domElement->getAttribute('value'));
                $text = trim($domElement->textContent);

                if ($value && $value != '--') {
                    $make = $this->entityManager->getRepository('WbcCrawlerBundle:ClassifiedsMake')
                        ->findOneBy(array('source' => $this->source, 'sourceId' => $value));

                    if (!$make) {
                        $make = new ClassifiedsMake();
                        $make->setSource($this->source);
                        $make->setName($text);
                        $make->setSourceId($value);
                        $this->entityManager->persist($make);
                    }

                    if ($this->overwrite) {
                        $make->setName($text);
                    }

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

        $this->outputInterface->writeln(sprintf('<info>Done Crawling MAKES from %s (%s)</info>', $this->siteName, $this->source));
    }
}
