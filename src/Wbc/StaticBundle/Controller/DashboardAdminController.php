<?php

declare(strict_types=1);

namespace Wbc\StaticBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DashboardAdminController.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @CF\Security("has_role('ROLE_ADMIN')")
 */
class DashboardAdminController extends Controller
{
    /**
     * @CF\Route("/dashboard/stats/{dateFrom}/{dateTo}/{grouping}", defaults={"_format": "json", "grouping": "day"})
     * @CF\Method("GET")
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @param string $grouping
     *
     * @return Response
     */
    public function getStatsAction($dateFrom, $dateTo, $grouping)
    {
        $fromDateString = $this->resetDateTime(new \DateTime($dateFrom))->format('Y-m-d H:i:s');
        $toDateString = $this->resetDateTime(new \DateTime($dateTo), 23, 59)->format('Y-m-d H:i:s');
        $dateFrom = new \DateTime($fromDateString);
        $dateTo = new \DateTime($toDateString);

        $dashboardManager = $this->container->get('wbc.static.dashboard_manager');
        $serializer = $this->container->get('serializer');

        $stats = [
            'valuations' => $serializer->serialize($dashboardManager->getValuations($dateFrom, $dateTo, $grouping), 'json'),
            'valuationsWithoutPrice' => $serializer->serialize($dashboardManager->getValuations($dateFrom, $dateTo, $grouping, false), 'json'),
            'appointments' => $serializer->serialize($dashboardManager->getAppointments($dateFrom, $dateTo, $grouping), 'json'),
            'appointmentsNoShow' => $serializer->serialize($dashboardManager->getAppointments($dateFrom, $dateTo, $grouping, false), 'json'),
            'inspections' => $serializer->serialize($dashboardManager->getInspections($dateFrom, $dateTo, $grouping), 'json'),
            'deals' => $serializer->serialize($dashboardManager->getDeals($dateFrom, $dateTo, $grouping), 'json'),
        ];

        return new Response(json_encode($stats));
    }

    private function resetDateTime(\DateTime $dateTime, $hour = 0, $min = 0, $sec = 0): \DateTime
    {
        return $dateTime->setTime($hour, $min, $sec);
    }
}
