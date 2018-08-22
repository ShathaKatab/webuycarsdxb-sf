<?php

declare(strict_types=1);

namespace Wbc\UtilityBundle\Twig;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Wbc\BranchBundle\Entity\AppointmentReminder;

/**
 * Class RequestedCallbackTwigExtension.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.utility.twig.requested_callback_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class RequestedCallbackTwigExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * RequestedCallbackTwigExtension constructor.
     *
     * @DI\InjectParams({
     *  "entityManager" = @DI\Inject("doctrine.orm.entity_manager")
     * })
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return
            [
                new \Twig_SimpleFunction('totalRequestedCallback', [$this, 'getTotalRequestedCallback']),
            ];
    }

    /**
     * @return int
     */
    public function getTotalRequestedCallback()
    {
        $appointmentReminders = $this->entityManager
            ->getRepository(AppointmentReminder::class)
            ->findBy(['isReschedule' => true, 'status' => AppointmentReminder::STATUS_NEW]);

        return count($appointmentReminders);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'requestedCallback';
    }
}
