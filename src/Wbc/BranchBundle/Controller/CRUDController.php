<?php

namespace Wbc\BranchBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wbc\BranchBundle\Entity\Branch;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;

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
}
