<?php

namespace Wbc\BranchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Component\HttpFoundation\Response;
use Wbc\BranchBundle\Entity\Branch;

/**
 * Class BranchController.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class BranchController extends Controller
{
    /**
     * Gets Branch Timings by Branch and Date.
     *
     * @CF\Route("/{branchSlug}/timings/{dayBooked}", name="wbc_branch_timing")
     * @CF\Method({"GET", "POST"})
     * @CF\ParamConverter("branch", class="WbcBranchBundle:Branch", options={"mapping": {"branchSlug"="slug"}})
     *
     * @param Branch $branch
     * @param string $dayBooked
     *
     * @return array
     */
    public function getTimings(Branch $branch, $dayBooked)
    {
        $branchTimings = $this->get('doctrine.orm.default_entity_manager')
            ->getRepository('WbcBranchBundle:Timing')->findAllByBranchAndDay($branch, (int) $dayBooked);

        return new Response($this->get('serializer')->serialize($branchTimings, 'json'), Response::HTTP_OK, ['content-type' => 'application/json']);
    }
}
