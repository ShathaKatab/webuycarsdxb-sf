<?php

use Symfony\Component\Config\Loader\LoaderInterface;
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
}

//APC Polyfills for PHP7
if (!function_exists('apc_fetch')) {
    function apc_fetch($key, &$success = null)
    {
        return apcu_fetch($key, $success);
    }
}

if (!function_exists('apc_exists')) {
    function apc_exists($keys)
    {
        return apcu_exists($keys);
    }
}
if (!function_exists('apc_store')) {
    function apc_store($keys)
    {
        return apcu_store($keys);
    }
}
