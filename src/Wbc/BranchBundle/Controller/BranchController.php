<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Wbc\BranchBundle\Entity\Branch;
use Wbc\BranchBundle\Entity\Timing;

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
     * @CF\Route("/{branchSlug}/timings/{dateBooked}",
     *     requirements={"dateBooked": "^\d{4}-\d{2}-\d{2}$"},
     *     name="wbc_branch_timing",
     *     methods={"GET"})
     * @CF\ParamConverter("branch", class="WbcBranchBundle:Branch", options={"mapping": {"branchSlug"="slug"}})
     *
     * @param Branch $branch
     * @param string $dateBooked
     *
     * @return Response
     */
    public function getTimings(Branch $branch, string $dateBooked)
    {
        try{
            $date = new \DateTime($dateBooked);
        }catch (\Exception $e){
            return new JsonResponse([]);
        }
        $branchTimings = $this->get('timing_repository')->findAllByBranchAndDate($branch, $date);

        return new Response(
            $this->get('serializer')->serialize($branchTimings, 'json'),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}
