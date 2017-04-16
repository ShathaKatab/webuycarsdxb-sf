<?php

namespace Wbc\BranchBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use JMS\DiExtraBundle\Annotation as DI;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Entity\AppointmentDetails;

/**
 * Class AppointmentListener.
 *
 * @DI\DoctrineListener(
 *     events = {"postPersist", "postUpdate", "postLoad"},
 *     connection = "default",
 *     lazy = true,
 *     priority = 0,
 * )
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class AppointmentListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof Appointment) {
            return;
        }

        $branch = $object->getBranch();
        $branchTiming = $object->getBranchTiming();

        if (!$branch && $branchTiming) {
            $object->setBranch($branchTiming->getBranch());
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof Appointment) {
            return;
        }

        $this->updateAppointmentDetails($object, $args->getObjectManager());
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof Appointment) {
            return;
        }

        $this->updateAppointmentDetails($object, $args->getObjectManager());
    }

    private function updateAppointmentDetails(Appointment $appointment, ObjectManager $objectManager)
    {
        $details = $appointment->getDetails();

        if (!$details) {
            $details = new AppointmentDetails($appointment, $appointment->getBranch(), $appointment->getBranchTiming());
            $objectManager->persist($details);
        }

        if ($appointment->getVehicleMake()) {
            $details->setVehicleMakeName($appointment->getVehicleMake()->getName());
        }

        if ($appointment->getVehicleModel()) {
            $details->setVehicleModelName($appointment->getVehicleModel()->getName());
        }

        $objectManager->flush();
    }
}
