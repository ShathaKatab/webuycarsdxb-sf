<?php

namespace Wbc\BranchBundle\Repository;

use Wbc\UtilityBundle\ORM\EntityRepository;

/**
 * Class BranchRepository.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class BranchRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function findAll()
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->where('b.active = :active')
            ->orderBy('b.name', 'ASC')
            ->setParameter(':active', true, \PDO::PARAM_BOOL);

        return $queryBuilder->getQuery()->getResult();
    }
}
