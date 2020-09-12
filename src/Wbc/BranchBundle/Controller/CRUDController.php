<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wbc\BranchBundle\BranchEvents;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Entity\Branch;
use Wbc\BranchBundle\Entity\Inspection;
use Wbc\BranchBundle\Events\AppointmentEvent;
use Wbc\InventoryBundle\Entity\Inventory;

/**
 * Class CRUDController.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class CRUDController extends Controller
{
    /**
     * Lists Vehicle Models by Make.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listVehicleModelsByMakeAction(Request $request)
    {
        return new Response($this->get('jms_serializer')->serialize(
            $this->get('doctrine.orm.default_entity_manager')
                ->getRepository('WbcVehicleBundle:Model')
                ->findByMakeId($request->get('id')),
            'json'));
    }

    /**
     * Lists Vehicle Models Types by Model.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listVehicleModelTypesByModelAction(Request $request)
    {
        return new Response($this->get('jms_serializer')->serialize(
            $this->get('doctrine.orm.default_entity_manager')
                ->getRepository('WbcVehicleBundle:ModelType')
                ->findAllBy(null, $request->get('id')), 'json'));
    }

    /**
     * Lists Branch Timings by Branch and day.
     *
     * @CF\ParamConverter("branch", class="WbcBranchBundle:Branch", options={"mapping": {"branchId"="id"}})
     *
     * @param Branch  $branch
     * @param Request $request
     *
     * @return Response
     */
    public function listBranchTimingsAction(Branch $branch, Request $request)
    {
        $dateBooked = new \DateTime($request->get('date'));

        $timings = $this->get('doctrine.orm.default_entity_manager')->getRepository('WbcBranchBundle:Timing')
            ->findAllByBranchAndDate($branch, $dateBooked, true);

        return new Response($this->get('jms_serializer')->serialize($timings, 'json'));
    }

    /**
     * Generates an Inspection from an Appointment.
     *
     * @CF\ParamConverter("appointment", class="WbcBranchBundle:Appointment")
     *
     * @param Appointment $appointment
     *
     * @return Response
     */
    public function generateInspectionAction(Appointment $appointment)
    {
        $entityManager = $this->get('doctrine.orm.default_entity_manager');
        $inspection = new Inspection($appointment);
        $inspection->setCreatedBy($this->getUser());
        $entityManager->persist($inspection);
        $entityManager->flush();

        $this->get('event_dispatcher')
            ->dispatch(BranchEvents::ON_APPOINTMENT_GENERATE_INSPECTION, new AppointmentEvent($appointment));

        return new RedirectResponse($this->generateUrl('admin_wbc_branch_inspection_show', ['id' => $inspection->getId()]));
    }

    /**
     * Sends an Appointment SMS to customer.
     *
     * @CF\ParamConverter("appointment", class="WbcBranchBundle:Appointment")
     *
     * @param Appointment $appointment
     *
     * @return Response
     */
    public function sendSmsAction(Appointment $appointment)
    {
        $smsManager = $this->get('wbc.utility.twilio_manager');

        $response = $smsManager
            ->sendSms($appointment->getMobileNumber(), $this->get('templating')->render('WbcBranchBundle::appointmentSms.txt.twig', ['appointment' => $appointment]));

        if ($response instanceof \Twilio\Rest\Api\V2010\Account\MessageInstance) {
            $appointment->setSmsSent(true);
            $this->get('doctrine.orm.default_entity_manager')->flush($appointment);
        }

        $queryBuilder = $this->get('doctrine.orm.default_entity_manager')->createQueryBuilder();
        $queryBuilder->select('u')->from('WbcUserBundle:User', 'u')
            ->where($queryBuilder->expr()->like('u.roles', $queryBuilder->expr()->literal('%ROLE_APPOINTMENT_SMS%')));

        $roleAppointmentSmsUsers = $queryBuilder->getQuery()->getResult();

        foreach ($roleAppointmentSmsUsers as $user) {
            $profile = $user->getProfile();
            if ($profile) {
                $smsManager->sendSms($profile->getMobileNumber(), $this->get('templating')->render('WbcBranchBundle:Admin:appointmentSms.txt.twig', ['appointment' => $appointment]));
            }
        }

        return new Response('');
    }
}
