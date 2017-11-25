<?php

namespace Wbc\ValuationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Router;
use Wbc\BranchBundle\BranchEvents;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Events\AppointmentEvent;
use Wbc\ValuationBundle\Entity\Valuation;
use Wbc\ValuationBundle\Form\AppointmentType;
use Wbc\ValuationBundle\Form\ValuationStepOneType;
use Wbc\ValuationBundle\Form\ValuationStepThreeType;
use Wbc\ValuationBundle\Form\ValuationStepTwoType;
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
     * Step 1.
     *
     * @CF\Route("/car-valuation-1", name="wbc_appointment_step_1")
     * @CF\Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return array
     */
    public function step1Action(Request $request)
    {
        $form = null;

        if ($request->getMethod() === Request::METHOD_POST) {
            $data = $request->request->all();
            $form = $this->createForm(new ValuationStepOneType());

            $form->submit($data);

            if ($form->isValid()) {
                $formData = $form->getData();
                $session = $this->get('session');
                $session->set('modelId', $formData['vehicleModel']->getId());
                $session->set('modelYear', $formData['vehicleYear']);

                return $this->redirectToRoute('wbc_appointment_step_2');
            }
        }

        return ['form' => $form ? $form->createView() : null];
    }

    /**
     * Step 2.
     *
     * @CF\Route("/car-valuation-2", name="wbc_appointment_step_2")
     * @CF\Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return array
     */
    public function step2Action(Request $request)
    {
        $form = null;
        $session = $this->get('session');

        if (!$session->has('modelId') || !$session->has('modelYear')) {
            return $this->redirectToRoute('wbc_appointment_step_1');
        }

        $modelId = $session->get('modelId');
        $modelYear = $session->get('modelYear');
        $entityManager = $this->get('doctrine.orm.default_entity_manager');

        if ($request->getMethod() === Request::METHOD_POST) {
            $data = $request->request->all();
            $data['vehicleModel'] = $modelId;
            $data['vehicleYear'] = $modelYear;

            $valuation = new Valuation();

            $form = $this->createForm(new ValuationStepTwoType(), $valuation);

            $form->submit($data);

            if ($form->isValid()) {
                $entityManager->persist($valuation);
                $entityManager->flush();

                $session->set('valuationId', $valuation->getId());

                return $this->redirectToRoute('wbc_appointment_step_3');
            }
        }

        $model = $entityManager->getRepository('WbcVehicleBundle:Model')->find($modelId);

        if (!$model) {
            throw new NotFoundHttpException('Vehicle model is not found!');
        }

        $modelTypesData = [];

        $modelTypes = $entityManager->getRepository('WbcVehicleBundle:ModelType')
            ->findBy(['model' => $model, 'isGcc' => true]);

        if ($modelTypes) {
            foreach ($modelTypes as $modelType) {
                if (in_array($modelYear, $modelType->getYears(), true)) {
                    $modelTypesData[] = $modelType;
                }
            }
        }

        return [
            'vehicleModelTypes' => count($modelTypesData) ? $modelTypesData : $modelTypes,
            'vehicleModel' => $model,
            'vehicleYear' => $modelYear,
            'form' => $form ? $form->createView() : null,
        ];
    }

    /**
     * Step 3.
     *
     * @CF\Route("/car-valuation-3", name="wbc_appointment_step_3")
     * @CF\Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return array
     */
    public function step3Action(Request $request)
    {
        $session = $this->get('session');
        $entityManager = $this->get('doctrine.orm.default_entity_manager');

        $this->checkValuationInSession();

        $valuationId = $session->get('valuationId');
        $valuation = $entityManager->getRepository('WbcValuationBundle:Valuation')->find($valuationId);

        if (!$valuation) {
            throw new NotFoundHttpException('Valuation is not found!');
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            $data = $request->request->all();

            $form = $this->createForm(new ValuationStepThreeType(), $valuation);
            $form->submit($data);

            if ($form->isValid()) {
                $entityManager->persist($valuation);
                $entityManager->flush();

                return $this->redirectToRoute('wbc_appointment_step_4');
            }
            var_dump($form->getErrors());
            exit;
        }

        return ['valuation' => $valuation];
    }

    /**
     * Step 4.
     *
     * @CF\Route("/car-valuation-thank-you", name="wbc_appointment_step_4")
     * @CF\Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return array
     */
    public function step4Action(Request $request)
    {
        $form = null;
        $this->checkValuationInSession();
        $session = $this->get('session');
        $valuationId = $session->get('valuationId');
        $entityManager = $this->get('doctrine.orm.default_entity_manager');
        $valuation = $entityManager->getRepository(Valuation::class)->find($valuationId);

        if (!$valuation) {
            throw new NotFoundHttpException('Valuation is not found!');
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            $data = json_decode($request->getContent(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('Form is not valid JSON content!');
            }

            $data['valuation'] = $valuationId;

            $appointment = new Appointment($valuation);

            $form = $this->createForm(new AppointmentType(), $appointment);

            $form->submit($data);

            if ($form->isValid()) {
                $this->get('event_dispatcher')->dispatch(BranchEvents::BEFORE_APPOINTMENT_CREATE, new AppointmentEvent($appointment));
                $entityManager->persist($appointment);
                $entityManager->flush();

                $session->set('appointmentId', $appointment->getId());

                return new JsonResponse('', JsonResponse::HTTP_CREATED, [
                    'Location' => $this->generateUrl('wbc_appointment_step_5', [
                    ], Router::ABSOLUTE_URL),
                ]);
            }
        }

        return ['valuation' => $valuation, 'form' => $form ? $form->createView() : null];
    }

    /**
     * Step 5.
     *
     * @CF\Route("/car-appointment-thank-you", name="wbc_appointment_step_5")
     * @CF\Method({"GET"})
     *
     * @return array
     */
    public function step5Action()
    {
        $this->checkValuationInSession();
        $session = $this->get('session');
        $entityManager = $this->get('doctrine.orm.default_entity_manager');

        if (!$session->has('appointmentId')) {
            if ($session->has('valuationId')) {
                return $this->redirectToRoute('wbc_appointment_step_3');
            }

            if ($session->has('modelId') && $session->has('modelYear')) {
                return $this->redirectToRoute('wbc_appointment_step_2');
            }

            return $this->redirectToRoute('wbc_appointment_step_1');
        }

        $valuation = $entityManager->getRepository(Valuation::class)->find($session->get('valuationId'));

        if (!$valuation) {
            throw new NotFoundHttpException('Valuation is not found!');
        }

        $appointment = $entityManager->getRepository(Appointment::class)->find($session->get('appointmentId'));

        if (!$appointment) {
            throw new NotFoundHttpException('Appointment is not found!');
        }

        $session->remove('modelId');
        $session->remove('modelYear');
        $session->remove('valuationId');
        $session->remove('appointmentId');

        return ['valuation' => $valuation, 'appointment' => $appointment];
    }

    private function checkValuationInSession()
    {
        $session = $this->get('session');
        if (!$session->has('valuationId')) {
            if ($session->has('modelId') && $session->has('modelYear')) {
                return $this->redirectToRoute('wbc_appointment_step_2');
            }

            return $this->redirectToRoute('wbc_appointment_step_1');
        }
    }
}
