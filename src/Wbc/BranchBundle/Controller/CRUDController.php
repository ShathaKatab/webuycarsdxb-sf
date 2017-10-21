<?php

namespace Wbc\BranchBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wbc\BranchBundle\BranchEvents;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Entity\Branch;
use Wbc\BranchBundle\Entity\Deal;
use Wbc\BranchBundle\Entity\Inspection;
use Wbc\BranchBundle\Events\AppointmentEvent;

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
        return new Response($this->get('serializer')->serialize(
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
        return new Response($this->get('serializer')->serialize(
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
        return new Response($this->get('serializer')->serialize(
            $this->get('doctrine.orm.default_entity_manager')
                ->getRepository('WbcBranchBundle:Timing')
                ->findAllByBranchAndDay($branch, $request->get('day')), 'json'));
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
     * Generates a Deal from an Inspection.
     *
     * @CF\ParamConverter("inspection", class="WbcBranchBundle:Inspection")
     *
     * @param Inspection $inspection
     *
     * @return Response
     */
    public function generateDealAction(Inspection $inspection)
    {
        $entityManager = $this->get('doctrine.orm.default_entity_manager');
        $deal = new Deal($inspection);
        $deal->setCreatedBy($this->getUser());
        $entityManager->persist($deal);
        $entityManager->flush();

        return new RedirectResponse($this->generateUrl('admin_wbc_branch_deal_show', ['id' => $deal->getId()]));
    }
}
