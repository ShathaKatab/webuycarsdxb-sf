<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Wbc\BranchBundle\Entity\Branch;
use Wbc\BranchBundle\Entity\Timing;

/**
 * Class TimingRepository.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class TimingRepository extends EntityRepository
{
    private $noAppointmentDates = [];

    /**
     * @param Branch    $branch
     * @param \DateTime $dateBooked
     * @param bool      $admin
     * @param bool      $timeConstrained
     *
     * @return array
     */
    public function findByBranchAndDate(Branch $branch, \DateTime $dateBooked, bool $admin = false, bool $timeConstrained = true): array
    {
        $now       = new \DateTime();
        $dayBooked = $dateBooked->format('N');

        $queryBuilder = $this->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.branch', 'branch', 'WITH', 'branch = :branch')
            ->where('t.dayOfWeek = :dayOfWeek')
            ->setParameter('branch', $branch)
            ->setParameter('dayOfWeek', $dayBooked, \PDO::PARAM_INT)
            ->orderBy('t.dayOfWeek', 'ASC')
            ->addOrderBy('t.from', 'ASC')
        ;

        if ($timeConstrained && $dateBooked->format('Y-m-d') === $now->format('Y-m-d')) {
            $queryBuilder->andWhere('t.from >= :fromTime')
                ->setParameter(':fromTime', $now->format('H:i').':00')
            ;
        }

        if (false === $admin) {
            if (\in_array($dateBooked->format('Y-m-d'), $this->noAppointmentDates, true)) {
                return [];
            }
        }
        /** @var Timing $timing */
        $timing = $queryBuilder->getQuery()->getSingleResult();

        if (null === $timing) {
            return [];
        }

        $holiday = $this->getEntityManager()
            ->getRepository('Wbc\BranchBundle\Entity\Holiday')
            ->createQueryBuilder('h')
            ->where(':dateBooked BETWEEN h.from AND h.to')
            ->orWhere('(h.from=:dateBooked AND h.to=:dateBooked)')
            ->setParameter('dateBooked', $dateBooked->format('Y-m-d'))
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (null !== $holiday) {
            return [];
        }

        $branchName = $branch->getName();
        $dayOfWeek  = $timing->getDayOfWeek();

        $partial = [
            'branch' => [
                'name'         => $branchName,
                'slug'         => $branch->getSlug(),
                'latitude'     => $branch->getLatitude(),
                'longitude'    => $branch->getLongitude(),
                'address'      => $branch->getAddress(),
                'city_slug'    => $branch->getCitySlug(),
                'phone_number' => $branch->getPhoneNumber(),
            ],
            'day_of_week' => $dayOfWeek,
        ];

        $result = [];

        /** @var \DateTime $start */
        $start = $timing->getFrom();
        $end   = $timing->getTo();

        while ($start->format('H:i') < $end->format('H:i')) {
            $currentEnd = (clone $start)->add(new \DateInterval('PT30M'));
            $result[]   = array_merge($partial, [
                'name'       => Timing::getNameStatic($branchName, $dayOfWeek, $start, $currentEnd),
                'short_name' => Timing::getShortNameStatic($dayOfWeek, $start, $currentEnd),
                'from'       => $start->format('H:i'),
                'to'         => $currentEnd->format('H:i'),
            ]);
            $start->add(new \DateInterval('PT30M'));
        }

        return $result;
    }

    public function setNoAppointmentDates(array $noAppointmentDates = []): self
    {
        $this->noAppointmentDates = $noAppointmentDates;

        return $this;
    }
}
