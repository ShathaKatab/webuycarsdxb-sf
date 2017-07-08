<?php

namespace Wbc\BranchBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Wbc\BranchBundle\BranchEvents;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Entity\AppointmentDetails;
use Wbc\BranchBundle\Events\AppointmentEvent;
use Wbc\UserBundle\Entity\User;
use Wbc\UtilityBundle\TwilioManager;
use Wbc\ValuationBundle\Entity\Valuation;

/**
 * Class AppointmentListener.
 *
 * @DI\DoctrineListener(
 *     events = {"prePersist", "postPersist", "postUpdate", "postLoad"},
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

    /**
     * @var TwigEngine
     */
    private $templating;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * AppointmentListener Constructor.
     *
     * @DI\InjectParams({
     *  "entityManager" = @DI\Inject("doctrine.orm.default_entity_manager"),
     *  "twilioManager" = @DI\Inject("wbc.utility.twilio_manager"),
     *  "templating" = @DI\Inject("templating"),
     *  "tokenStorage" = @DI\Inject("security.token_storage")
     * })
     *
     * @param EntityManager         $entityManager
     * @param TwilioManager         $twilioManager
     * @param TwigEngine            $templating
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManager $entityManager, TwilioManager $twilioManager, TwigEngine $templating, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->smsManager = $twilioManager;
        $this->templating = $templating;
        $this->tokenStorage = $tokenStorage;
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
        $vehicleModel = $object->getVehicleModel();

        if (!$branch && $branchTiming) {
            $object->setBranch($branchTiming->getBranch());
        }

        if ($vehicleModel) {
            $object->setVehicleMake($vehicleModel->getMake());
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        if (!$object instanceof Appointment) {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if ($token && $token->getUser() instanceof User) {
            $object->setCreatedBy($token->getUser());
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $objectManager = $args->getObjectManager();

        if (!$object instanceof Appointment) {
            return;
        }

        $this->updateAppointmentDetails($object, $args->getObjectManager());

        if (!$object->getValuation()) {
            $valuation = new Valuation($object);
            $objectManager->persist($valuation);
            $objectManager->flush();
        }

        if ($object->getBranch() && $object->getSmsTimingString() && $object->getName()) {
            $this->smsManager->sendSms($object->getMobileNumber(),
                $this->templating->render('WbcBranchBundle::appointmentSms.txt.twig', [
                    'appointment' => $object,
                    'siteDomain' => 'WEBUYCARSDXB.COM',
                ]));

            $queryBuilder = $this->entityManager->createQueryBuilder();
            $queryBuilder->select('u')
                ->from('WbcUserBundle:User', 'u')
                ->where($queryBuilder->expr()->like('u.roles', $queryBuilder->expr()->literal('%ROLE_APPOINTMENT_SMS%')));
            $roleAppointmentSmsUsers = $queryBuilder->getQuery()->getResult();

            foreach ($roleAppointmentSmsUsers as $user) {
                $profile = $user->getProfile();

                if ($profile) {
                    $this->smsManager->sendSms($profile->getMobileNumber(),
                        $this->templating->render('WbcBranchBundle:Admin:appointmentSms.txt.twig', ['appointment' => $object]));
                }
            }
        }
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
