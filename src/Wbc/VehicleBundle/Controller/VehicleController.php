<?php

namespace Wbc\VehicleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Component\HttpFoundation\Response;
use Wbc\VehicleBundle\Entity\Make;

/**
 * Class VehicleController.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class VehicleController extends Controller
{
    /**
     * Gets Vehicle Models.
     *
     * @CF\Route("/make/{makeId}/models", name="wbc_vehicle_models")
     * @CF\Method("GET")
     * @CF\ParamConverter("make", class="WbcVehicleBundle:Make", options={"mapping": {"makeId"="id"}})
     *
     * @param Make $make
     *
     * @return Response
     */
    public function vehicleModelsForMakeAction(Make $make)
    {
        $models = $this->get('doctrine.orm.default_entity_manager')->getRepository('WbcVehicleBundle:Model')->findByMakeId($make->getId());

        return new Response($this->get('serializer')->serialize($models, 'json'), Response::HTTP_OK, ['content-type' => 'application/json']);
    }
}
