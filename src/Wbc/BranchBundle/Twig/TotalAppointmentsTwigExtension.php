<?php

namespace Wbc\BranchBundle\Twig;

use Doctrine\ORM\EntityRepository;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class TotalAppointmentsTwigExtension.
 * 
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.branch.twig.total_appointments_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class TotalAppointmentsTwigExtension extends \Twig_Extension
{
    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * BranchTwigExtension Constructor.
     *
     * @DI\InjectParams({
     *  "repository" = @DI\Inject("wbc.branch.appointment_repository")
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
        return [new \Twig_SimpleFunction('totalAppointments', [$this, 'totalAppointments'])];
    }

    /**
     * @return array
     */
    public function totalAppointments()
    {
        return $this->repository->totalAppointments();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'totalAppointments';
    }

}
