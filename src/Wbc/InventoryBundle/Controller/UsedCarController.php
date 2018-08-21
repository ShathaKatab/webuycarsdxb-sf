<?php

declare(strict_types=1);

namespace Wbc\InventoryBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wbc\InventoryBundle\Entity\UsedCar;

/**
 * Class UsedCarController.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class UsedCarController extends Controller
{
    /**
     * @CF\Route("/{guid}",
     *     requirements={"guid": "^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$"},
     *     name="wbc_inventory_used_car_get")
     * @CF\ParamConverter("usedCar", class="Wbc\InventoryBundle\Entity\UsedCar")
     *
     * @param UsedCar $usedCar
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUsedCarAction(UsedCar $usedCar)
    {
        return $this->render('@WbcInventory/Default/getUsedCar.html.twig', ['usedCar' => $usedCar]);
    }
}
