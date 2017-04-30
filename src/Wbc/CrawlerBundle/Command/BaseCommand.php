<?php

namespace Wbc\CrawlerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\Client;

/**
 * Class BaseCommand.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
abstract class BaseCommand extends ContainerAwareCommand
{
    protected $url = '';
    protected $userAgent = '';
    protected $source = '';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzleClient;

    /**
     * @var bool
     */
    protected $overwrite = false;
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface;
     */
    protected $outputInterface;

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->userAgent = $this->getContainer()->getParameter('crawler_user_agent');
        $this->entityManager = $this->getContainer()->get('doctrine')->getManager('default');
        $this->guzzleClient = new Client([
            'base_uri' => $this->url,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => $this->userAgent,
            ],
            'curl' => [
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_RETURNTRANSFER => 1,
            ],
            'debug' => false,
        ]);

        $this->outputInterface = $output;

        $overwrite = $input->getOption('overwrite');

        if ($overwrite) {
            $this->overwrite = true;
        }
    }
}
