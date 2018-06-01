<?php

declare(strict_types=1);

namespace Wbc\UsedCarsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wbc\UsedCarsBundle\Entity\UsedCars;

/**
 * Class DefaultController.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class DefaultController extends Controller
{
    /**
     * @CF\Route("/{guid}", requirements={"guid": "^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$"}, name="wbc_used_cars_get")
     * @CF\ParamConverter("usedCar", class="Wbc\UsedCarsBundle\Entity\UsedCars")
     *
     * @param UsedCars $usedCar
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUsedCarAction(UsedCars $usedCar)
    {
        return $this->render('@WbcUsedCars/Default/getUsedCar.html.twig', ['usedCar' => $usedCar]);
    }
}
