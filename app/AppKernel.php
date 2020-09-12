<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\AopBundle\JMSAopBundle(),
            new Lexik\Bundle\MaintenanceBundle\LexikMaintenanceBundle(),
            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new Craue\ConfigBundle\CraueConfigBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new SunCat\MobileDetectBundle\MobileDetectBundle(),
            new Bugsnag\BugsnagBundle\BugsnagBundle(),
            new Snc\RedisBundle\SncRedisBundle(),
            new Noxlogic\RateLimitBundle\NoxlogicRateLimitBundle(),

            new Wbc\UserBundle\WbcUserBundle(),
            new Wbc\UtilityBundle\WbcUtilityBundle(),
            new Wbc\BranchBundle\WbcBranchBundle(),
            new Wbc\VehicleBundle\WbcVehicleBundle(),
            new Wbc\CrawlerBundle\WbcCrawlerBundle(),
            new Wbc\StaticBundle\WbcStaticBundle(),
            new Wbc\ValuationBundle\WbcValuationBundle(),
            new Wbc\BlogBundle\WbcBlogBundle(),
            new Wbc\CareersBundle\WbcCareersBundle(),
            new Wbc\InventoryBundle\WbcInventoryBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        $file = new File('/dev/shm/appname/cache' . '/' . $this->environment, false);

        if (in_array($this->environment, ['dev', 'test'], true) && $file->isWritable()) {
            return '/dev/shm/appname/cache/'.$this->environment;
        }

        return dirname(__DIR__).'/var/cache/'.$this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        $file = new File('/dev/shm/appname/logs' . '/' . $this->environment, false);

        if (in_array($this->environment, ['dev', 'test'], true) && $file->isWritable()) {
            return '/dev/shm/appname/logs'.'/'.$this->environment;
        }

        return dirname(__DIR__).'/var/logs/';
    }
}
