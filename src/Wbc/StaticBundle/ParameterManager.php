<?php

declare(strict_types=1);

namespace Wbc\StaticBundle;

use Doctrine\ORM\EntityManagerInterface;
use JMS\DiExtraBundle\Annotation as DI;
use Stringy\Stringy as S;
use Wbc\StaticBundle\Entity\Parameter;
use Wbc\StaticBundle\Repository\ParameterRepository;

/**
 * Class ParameterManager.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.static.parameter_manager")
 */
class ParameterManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ParameterRepository
     */
    private $parameterRepository;

    /**
     * @var \Memcached
     */
    private $memcached;

    /**
     * ParameterManager constructor.
     *
     * @DI\InjectParams({
     *      "entityManager" = @DI\Inject("doctrine.orm.entity_manager"),
     *      "parameterRepository" = @DI\Inject("static.parameter.repository"),
     *      "memcached" = @DI\Inject("memcached")
     * })
     *
     * @param EntityManagerInterface $entityManager
     * @param ParameterRepository    $parameterRepository
     * @param \Memcached             $memcached
     */
    public function __construct(EntityManagerInterface $entityManager, ParameterRepository $parameterRepository, \Memcached $memcached)
    {
        $this->entityManager = $entityManager;
        $this->parameterRepository = $parameterRepository;
        $this->memcached = $memcached;
    }

    public function getValuationSources()
    {
        $sources = [];
        $parameter = $this->parameterRepository->findOneByKey(Parameter::VALUATION_SOURCES);

        if ($parameter) {
            foreach ($parameter->getValue() as $source) {
                $sources[$source] = implode('-', array_map('ucfirst', explode('-', (string) S::create($source)->humanize())));
            }
        }

        return $sources;
    }

    public function addValuationSource(string $source): void
    {
        $source = $this->websitifySource($source);
        $valuationSources = array_keys($this->getValuationSources());

        if (in_array($source, $valuationSources, true)) {
            return;
        }

        array_unshift($valuationSources, $source);
        $parameter = $this->parameterRepository->findOneBy(['key' => Parameter::VALUATION_SOURCES]);

        if ($parameter) {
            $parameter->setValue($valuationSources);
            $this->entityManager->flush();
            $this->memcached->delete(Parameter::VALUATION_SOURCES);//let's invalidate memcached
        }
    }

    public function websitifySource(string $source)
    {
        $source = S::create($source)->slugify()->toLowerCase();

        if (!$source->startsWith('website-')) {
            $source = $source->prepend('website-');
        }

        return (string) $source;
    }
}
