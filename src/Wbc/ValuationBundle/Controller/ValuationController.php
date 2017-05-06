<?php

namespace Wbc\ValuationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
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
     * @param Model $model
     * @param int   $year
     *
     * @return array
     */
    public function detailsAction(Model $model, $year)
    {
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

        return ['modelTypes' => count($modelTypesData) ? $modelTypesData : $modelTypes];
    }

    /**
     * Step 3 (Book Appointment).
     *
     * @CF\Route("/{valuationId}/appointment", name="wbc_valuation_appointment")
     * @CF\Method({"GET", "POST"})
     *
     * @return array
     */
    public function appointmentAction()
    {
        return [];
    }

    /**
     * Step 4 (Appointment Success Confirmation).
     *
     * @CF\Route("/{valuationId}/appointment/{appointmentId}", name="wbc_valuation_appointment_success_confirmation")
     * @CF\Method({"GET"})
     *
     * @return array
     */
    public function appointmentSuccessConfirmationAction()
    {
        return [];
    }
}
