<?php

namespace Wbc\BranchBundle\EventListener;

use UtilityBundle\TwilioManager;
use Wbc\BranchBundle\BranchEvents;
use Wbc\BranchBundle\Events\AppointmentEvent;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use JMS\DiExtraBundle\Annotation as DI;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Entity\AppointmentDetails;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;

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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TwilioManager
     */
    private $smsManager;

    private $templating;

    /**
     * AppointmentListener Constructor.
     *
     * @DI\InjectParams({
     *  "entityManager" = @DI\Inject("doctrine.orm.default_entity_manager"),
     *  "twilioManager" = @DI\Inject("wbc.utility.twilio_manager"),
     *  "templating" = @DI\Inject("templating")
     * })
     *
     * @param EntityManager $entityManager
     * @param TwilioManager $twilioManager
     * @param TwigEngine    $templating
     */
    public function __construct(EntityManager $entityManager, TwilioManager $twilioManager, TwigEngine $templating)
    {
        $this->entityManager = $entityManager;
        $this->smsManager = $twilioManager;
        $this->templating = $templating;
    }

    /**
     * @DI\Observe(BranchEvents::BEFORE_APPOINTMENT_CREATE)
     *
     * @param AppointmentEvent $event
     */
    public function onBeforeCreate(AppointmentEvent $event)
    {
        $appointment = $event->getAppointment();

        if (!$appointment instanceof Appointment) {
            return;
        }

        $existingAppointment = $this->entityManager->getRepository('WbcBranchBundle:Appointment')->findOneBy([
            'valuation' => $appointment->getValuation(),
        ]);

        if ($existingAppointment) {
            $this->entityManager->remove($existingAppointment);
            $this->entityManager->flush();
        }
    }

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
        $this->smsManager->sendSms($object->getMobileNumber(), $this->templating->render('WbcBranchBundle::appointmentSms.txt.twig', ['appointment' => $object, 'siteDomain' => 'WEBUYCARSDXB.COM']));
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

        if ($vehicleModel = $appointment->getVehicleModel()) {
            $details->setVehicleMakeName($vehicleModel->getMake()->getName());
            $details->setVehicleModelName($vehicleModel->getName());
        }

        if ($vehicleModelType = $appointment->getVehicleModelType()) {
            $details->setVehicleModelTypeName($vehicleModelType->getName());
        }

        $objectManager->flush();
    }
}
