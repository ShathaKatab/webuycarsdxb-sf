<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Wbc\BranchBundle\BranchEvents;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Entity\AppointmentDetails;
use Wbc\BranchBundle\Events\AppointmentEvent;
use Wbc\StaticBundle\EventListener\StaticListener;
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
     * @var Session
     */
    private $session;

    /**
     * AppointmentListener Constructor.
     *
     * @DI\InjectParams({
     *  "entityManager" = @DI\Inject("doctrine.orm.default_entity_manager"),
     *  "twilioManager" = @DI\Inject("wbc.utility.twilio_manager"),
     *  "templating" = @DI\Inject("templating"),
     *  "tokenStorage" = @DI\Inject("security.token_storage"),
     *  "session" = @DI\Inject("session")
     * })
     *
     * @param EntityManager         $entityManager
     * @param TwilioManager         $twilioManager
     * @param TwigEngine            $templating
     * @param TokenStorageInterface $tokenStorage
     * @param Session               $session
     */
    public function __construct(EntityManager $entityManager,
                                TwilioManager $twilioManager,
                                TwigEngine $templating,
                                TokenStorageInterface $tokenStorage,
                                Session $session)
    {
        $this->entityManager = $entityManager;
        $this->smsManager = $twilioManager;
        $this->templating = $templating;
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    /**
     * @DI\Observe(BranchEvents::BEFORE_APPOINTMENT_CREATE)
     *
     * @param AppointmentEvent $event
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onBeforeCreate(AppointmentEvent $event): void
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
    public function postLoad(LifecycleEventArgs $args): void
    {
        /**@var Appointment $object*/
        $object = $args->getObject();

        if (!$object instanceof Appointment) {
            return;
        }

        $vehicleModel = $object->getVehicleModel();

        if ($vehicleModel) {
            $object->setVehicleMake($vehicleModel->getMake());
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        /**@var Appointment $object*/
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
     * @throws \Twig\Error\Error
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        /**@var Appointment $object*/
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

        //The user has created an Appointment
        //Remove the utm_source from the session
        if ($this->session->has(StaticListener::UTM_SOURCE)) {
            $this->session->remove(StaticListener::UTM_SOURCE);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Appointment) {
            return;
        }

        $this->updateAppointmentDetails($object, $args->getObjectManager());
    }

    /**
     * @DI\Observe(BranchEvents::ON_APPOINTMENT_GENERATE_INSPECTION)
     *
     * @param AppointmentEvent $event
     */
    public function onAppointmentGenerateInspection(AppointmentEvent $event): void
    {
        $appointment = $event->getAppointment();
        $appointment->setStatus(Appointment::STATUS_INSPECTED);
        $this->entityManager->flush();
    }

    /**
     * @param Appointment   $appointment
     * @param ObjectManager $objectManager
     */
    private function updateAppointmentDetails(Appointment $appointment, ObjectManager $objectManager): void
    {
        $details = $appointment->getDetails();

        if (!$details) {
            $details = new AppointmentDetails($appointment, $appointment->getBranch());
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
