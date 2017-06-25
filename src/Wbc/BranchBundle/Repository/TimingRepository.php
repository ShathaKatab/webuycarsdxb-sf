<?php

namespace Wbc\BranchBundle\Repository;

use Wbc\BranchBundle\Entity\Branch;
use Doctrine\ORM\EntityRepository;
use Wbc\BranchBundle\Entity\Timing;

/**
 * Class TimingRepository.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class TimingRepository extends EntityRepository
{
    /**
     * @param Branch $branch
     * @param int    $dayBooked
     * @param bool   $timeConstrained
     *
     * @return array
     */
    public function findAllByBranchAndDay(Branch $branch, $dayBooked, $timeConstrained = true)
    {
        $now = new \DateTime();

        $queryBuilder = $this->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.branch', 'branch', 'WITH', 'branch = :branch')
            ->where('t.dayBooked = :dayBooked')
            ->setParameter('branch', $branch)
            ->setParameter('dayBooked', $dayBooked, \PDO::PARAM_INT)
            ->orderBy('t.dayBooked', 'ASC')
            ->addOrderBy('t.from', 'ASC');

        if ($timeConstrained && intval($dayBooked) == $now->format('N')) {
            $queryBuilder->andWhere('t.from >= :fromTime')
                ->setParameter(':fromTime', Timing::formatDateTimeToInteger($now));
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
