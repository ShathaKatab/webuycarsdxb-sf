<?php

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
     * @param Branch $branch
     * @param \DateTime $dateBooked
     * @param bool $admin
     * @param bool $timeConstrained
     *
     * @return array
     */
    public function findAllByBranchAndDate(Branch $branch, \DateTime $dateBooked, $admin = false, $timeConstrained = true)
    {
        $now = new \DateTime();
        $dayBooked = $dateBooked->format('N');

        $queryBuilder = $this->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.branch', 'branch', 'WITH', 'branch = :branch')
            ->where('t.dayBooked = :dayBooked')
            ->setParameter('branch', $branch)
            ->setParameter('dayBooked', $dayBooked, \PDO::PARAM_INT)
            ->orderBy('t.dayBooked', 'ASC')
            ->addOrderBy('t.from', 'ASC');

        if ($timeConstrained && $dateBooked->format('Y-m-d') === $now->format('Y-m-d')) {
            $queryBuilder->andWhere('t.from >= :fromTime')
                ->setParameter(':fromTime', Timing::formatDateTimeToInteger($now));
        }

        if (false === $admin) {
            $queryBuilder->andWhere('t.adminOnly = :falsy')
                ->setParameter(':falsy', false, \PDO::PARAM_BOOL);

            if (in_array($dateBooked->format('Y-m-d'), $this->noAppointmentDates)) {
                return [];
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function setNoAppointmentDates(array $noAppointmentDates = []): self
    {
        $this->noAppointmentDates = $noAppointmentDates;

        return $this;
    }
}
