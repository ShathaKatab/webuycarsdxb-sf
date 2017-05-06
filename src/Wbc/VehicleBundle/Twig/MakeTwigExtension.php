<?php

namespace Wbc\VehicleBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;
use Wbc\UtilityBundle\ORM\EntityRepository;

/**
 * Class MakeTwigExtension.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.vehicle.twig.make_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class MakeTwigExtension extends \Twig_Extension
{
    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * BranchTwigExtension Constructor.
     *
     * @DI\InjectParams({
     *  "repository" = @DI\Inject("wbc.vehicle.repository.make_repository")
     * })
     *
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('getMakes', [$this, 'getMakes'])];
    }

    /**
     * @return array
     */
    public function getMakes()
    {
        return $this->repository->findAll();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'getMakes';
    }
}
