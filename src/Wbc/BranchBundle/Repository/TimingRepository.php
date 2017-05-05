<?php

namespace Wbc\BranchBundle\Repository;

use Wbc\BranchBundle\Entity\Branch;
use Doctrine\ORM\EntityRepository;
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
     *
     * @return array
     */
    public function findAllByBranchAndDay(Branch $branch, $dayBooked)
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.branch', 'branch', 'WITH', 'branch = :branch')
            ->where('t.dayBooked = :dayBooked')
            ->setParameter('branch', $branch)
            ->setParameter('dayBooked', $dayBooked)
            ->orderBy('t.dayBooked', 'ASC')
            ->addOrderBy('t.from', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }
}
