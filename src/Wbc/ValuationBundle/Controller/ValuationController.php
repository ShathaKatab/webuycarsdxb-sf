<?php

namespace Wbc\ValuationBundle\Controller;

use Wbc\BranchBundle\BranchEvents;
use Wbc\BranchBundle\Events\AppointmentEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Wbc\ValuationBundle\Form\AppointmentType;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\ValuationBundle\Entity\Valuation;
use Wbc\ValuationBundle\Form\ValuationType;
use Wbc\VehicleBundle\Entity\Model;

/**
 * Class ValuationController.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @CF\Template()
 */
class ValuationController extends Controller
{
    /**
     * Step 1 (Vehicle Make, Vehicle Model, Vehicle Year).
     *
     * @CF\Route("", name="wbc_valuation_index")
     * @CF\Method({"GET", "POST"})
     *
     * @return array
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * Step 2 (Vehicle and Personal Details).
     *
     * @CF\Route("/model/{modelId}/{year}", name="wbc_valuation_details")
     * @CF\Method({"GET", "POST"})
     * @CF\ParamConverter("model", class="WbcVehicleBundle:Model", options={"mapping": {"modelId"="id"}})
     *
     * @param Model   $model
     * @param int     $year
     * @param Request $request
     *
     * @return array
     */
    public function detailsAction(Model $model, $year, Request $request)
    {
        $form = null;

        if ($request->getMethod() == Request::METHOD_POST) {
            $data = $request->request->all();
            $data['vehicleModel'] = $model->getId();
            $data['vehicleYear'] = $year;

            $valuation = new Valuation();

            $form = $this->createForm(new ValuationType(), $valuation);

            $form->submit($data);

            if ($form->isValid()) {
                $entityManager = $this->get('doctrine.orm.default_entity_manager');
                $entityManager->persist($valuation);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('wbc_valuation_appointment', [
                    'valuationId' => $valuation->getId(),
                ]));
            }
        }

        $modelTypesData = [];

        $modelTypes = $this->get('doctrine.orm.default_entity_manager')
            ->getRepository('WbcVehicleBundle:ModelType')
            ->findBy(['model' => $model, 'isGcc' => true]);

        if ($modelTypes) {
            foreach ($modelTypes as $modelType) {
                if (in_array($year, $modelType->getYears())) {
                    $modelTypesData[] = $modelType;
                }
            }
        }

        return [
            'vehicleModelTypes' => count($modelTypesData) ? $modelTypesData : $modelTypes,
            'vehicleModel' => $model,
            'vehicleYear' => $year,
            'form' => $form ? $form->createView() : null,
        ];
    }

    /**
     * Step 3 (Book Appointment).
     *
     * @CF\Route("/{valuationId}/appointment", name="wbc_valuation_appointment")
     * @CF\Method({"GET", "POST"})
     * @CF\ParamConverter("valuation", class="WbcValuationBundle:Valuation", options={"mapping": {"valuationId"="id"}})
     *
     * @param Valuation $valuation
     * @param Request   $request
     *
     * @return array
     */
    public function appointmentAction(Valuation $valuation, Request $request)
    {
        $form = null;

        if ($request->getMethod() == Request::METHOD_POST) {
            $data = json_decode($request->getContent(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('Form is not valid JSON content!');
            }

            $data['valuation'] = $valuation->getId();

            $appointment = new Appointment($valuation);

            $form = $this->createForm(new AppointmentType(), $appointment);

            $form->submit($data);

            if ($form->isValid()) {
                $this->get('event_dispatcher')->dispatch(BranchEvents::BEFORE_APPOINTMENT_CREATE, new AppointmentEvent($appointment));
                $entityManager = $this->get('doctrine.orm.default_entity_manager');
                $entityManager->persist($appointment);
                $entityManager->flush();

                return new JsonResponse('', JsonResponse::HTTP_CREATED, [
                    'Location' => $this->generateUrl('wbc_valuation_appointment_success_confirmation', [
                        'valuationId' => $valuation->getId(),
                        'appointmentId' => $appointment->getId(),
                    ], Router::ABSOLUTE_URL),
                ]);
            }
        }

        return ['valuation' => $valuation, 'form' => $form ? $form->createView() : null];
    }

    /**
     * Step 4 (Appointment Success Confirmation).
     *
     * @CF\Route("/{valuationId}/appointment/{appointmentId}", name="wbc_valuation_appointment_success_confirmation")
     * @CF\Method({"GET"})
     * @CF\ParamConverter("valuation", class="WbcValuationBundle:Valuation", options={"mapping": {"valuationId"="id"}})
     * @CF\ParamConverter("appointment", class="WbcBranchBundle:Appointment", options={"mapping": {"appointmentId"="id"}})
     *
     * @param Valuation   $valuation
     * @param Appointment $appointment
     *
     * @return array
     */
    public function appointmentSuccessConfirmationAction(Valuation $valuation, Appointment $appointment)
    {
        return ['valuation' => $valuation, 'appointment' => $appointment];
    }
}
