<?php

declare(strict_types=1);

namespace Wbc\StaticBundle;

use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\StaticBundle\Model\Stat;
use Wbc\StaticBundle\Model\StatItem;

/**
 * Class DashboardManager.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.static.dashboard_manager")
 */
class DashboardManager
{
    const CACHE_LIFETIME = 86400;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * DashboardManager constructor.
     *
     * @DI\InjectParams({
     *     "entityManager" = @DI\Inject("doctrine.orm.default_entity_manager"),
     *     "memcachedCache" = @DI\Inject("memcached_cache")
     * })
     *
     * @param EntityManager  $entityManager
     * @param MemcachedCache $memcachedCache
     */
    public function __construct(EntityManager $entityManager, MemcachedCache $memcachedCache)
    {
        $this->connection = $entityManager->getConnection();
        $this->connection->getConfiguration()->setResultCacheImpl($memcachedCache);
    }

    public function getValuations(\DateTime $dateFrom, \DateTime $dateTo, $hasPrice = null): Stat
    {
        $stat = new Stat($dateFrom, $dateTo);

        $statItems = [];

        $sql = 'SELECT created_at, YEAR(created_at) AS year, MONTH(created_at) AS month, DAY(created_at) AS day, COUNT(1) AS total
                FROM valuation
                WHERE created_at >= ? 
                AND created_at <= ?
                %s
                GROUP BY YEAR(created_at), MONTH(created_at), DAY(created_at)
                ';
        if (false === $hasPrice) {
            $sql = sprintf($sql, ' AND price_online IS NULL ');
        } elseif (true === $hasPrice) {
            $sql = sprintf($sql, ' AND price_online IS NOT NULL ');
        } else {
            $sql = sprintf($sql, '');
        }

        $valuations = $this->performCachedQuery(
            $sql,
            [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')],
            [\PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR],
            $dateFrom,
            $dateTo,
            'valuation_stats'
        );

        foreach ($valuations as $valuation) {
            $statItems[] = new StatItem(new \DateTime($valuation['created_at']), (int) $valuation['total']);
        }

        $stat->items = $statItems;

        return $stat;
    }

    public function getAppointments(\DateTime $dateFrom, \DateTime $dateTo, $showedUp = null): Stat
    {
        $parameters = [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')];
        $types = [\PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR];

        $stat = new Stat($dateFrom, $dateTo);

        $statItems = [];

        $sql = 'SELECT created_at, YEAR(created_at) AS year, MONTH(created_at) AS month, DAY(created_at) AS day, COUNT(1) AS total
                FROM appointment
                WHERE created_at >= ? 
                AND created_at <= ? 
                %s
                GROUP BY YEAR(created_at), MONTH(created_at), DAY(created_at)
                ';

        if (false === $showedUp || true === $showedUp) {
            $sql = sprintf($sql, ' AND status = ? ');
        } elseif (true === $showedUp) {
            $sql = sprintf($sql, ' AND status <> ? ');
        } else {
            $sql = sprintf($sql, '');
        }

        if (false === $showedUp || true === $showedUp) {
            $parameters[] = Appointment::STATUS_INSPECTED;
            $types[] = \PDO::PARAM_STR;
        }

        $appointments = $this->performCachedQuery(
            $sql,
            $parameters,
            $types,
            $dateFrom,
            $dateTo,
            'appointment_stats'
        );

        foreach ($appointments as $appointment) {
            $statItems[] = new StatItem(new \DateTime($appointment['created_at']), (int) $appointment['total']);
        }

        $stat->items = $statItems;

        return $stat;
    }

    public function getInspections(\DateTime $dateFrom, \DateTime $dateTo): Stat
    {
        $stat = new Stat($dateFrom, $dateTo);

        $statItems = [];

        $sql = 'SELECT created_at, YEAR(created_at) AS year, MONTH(created_at) AS month, DAY(created_at) AS day, COUNT(1) AS total
                FROM inspection
                WHERE created_at >= ? 
                AND created_at <= ? 
                GROUP BY YEAR(created_at), MONTH(created_at), DAY(created_at)
                ';

        $inspections = $this->performCachedQuery(
            $sql,
            [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')],
            [\PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR],
            $dateFrom,
            $dateTo,
            'inspection_stats'
        );

        foreach ($inspections as $inspection) {
            $statItems[] = new StatItem(new \DateTime($inspection['created_at']), (int) $inspection['total']);
        }

        $stat->items = $statItems;

        return $stat;
    }

    public function getDeals(\DateTime $dateFrom, \DateTime $dateTo): Stat
    {
        $stat = new Stat($dateFrom, $dateTo);

        $statItems = [];

        $sql = 'SELECT created_at, YEAR(created_at) AS year, MONTH(created_at) AS month, DAY(created_at) AS day, COUNT(1) AS total
                FROM deal
                WHERE created_at >= ? 
                AND created_at <= ? 
                GROUP BY YEAR(created_at), MONTH(created_at), DAY(created_at)
                ';

        $deals = $this->performCachedQuery(
            $sql,
            [$dateFrom->format('Y-m-d H:i:s'), $dateTo->format('Y-m-d H:i:s')],
            [\PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR],
            $dateFrom,
            $dateTo,
            'deal_stats'
        );

        foreach ($deals as $deal) {
            $statItems[] = new StatItem(new \DateTime($deal['created_at']), (int) $deal['total']);
        }

        $stat->items = $statItems;

        return $stat;
    }

    private function performCachedQuery(string $sql,
                                        array $parameters,
                                        array $types,
                                        \DateTime $dateFrom,
                                        \DateTime $dateTo,
                                        string $keyPrefix): array
    {
        $statement = $this->connection->executeCacheQuery(
            $sql,
            $parameters,
            $types,
            new QueryCacheProfile(
                self::CACHE_LIFETIME,
                sprintf('%s_%s_%s', $keyPrefix, $dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d'))
            )
        );

        $data = $statement->fetchAll();

        $statement->closeCursor();

        return $data;
    }
}
