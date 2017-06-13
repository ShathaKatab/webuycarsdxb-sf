<?php

namespace Wbc\ValuationBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Wbc\BranchBundle\BranchEvents;
use Wbc\BranchBundle\Entity\Appointment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Wbc\BranchBundle\Events\AppointmentEvent;
use Wbc\ValuationBundle\Entity\Valuation;

/**
 * Class ValuationAdminController.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ValuationAdminController extends Controller
{
    /**
//     * @CF\Route("/{branchSlug}/timings/{dayBooked}", name="wbc_branch_timing")
//     * @CF\Method({"GET"})
     *
     * @CF\ParamConverter("valuation", class="WbcValuationBundle:Valuation")
     *
     * @param Valuation $valuation
     *
     * @return RedirectResponse
     *
     * @throws HttpException
     */
    public function generateAppointmentAction(Valuation $valuation)
    {
        $entityManager = $this->get('doctrine.orm.default_entity_manager');

        if ($valuation->hasAppointment()) {
            throw new HttpException(sprintf('Valuation with id: %s already has an Appointment', $valuation->getId()));
        }

        $appointment = new Appointment($valuation);
        $appointment->setDateBooked(new \DateTime());
        $this->get('event_dispatcher')->dispatch(BranchEvents::BEFORE_APPOINTMENT_CREATE, new AppointmentEvent($appointment));
        $entityManager->persist($appointment);
        $entityManager->flush();

        return new RedirectResponse($this->generateUrl('admin_wbc_branch_appointment_edit', ['id' => $appointment->getId()]));
    }
}
