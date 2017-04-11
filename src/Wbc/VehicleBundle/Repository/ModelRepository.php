<?php

namespace Wbc\VehicleBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class ModelRepository.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ModelRepository extends EntityRepository
{
    public function findByMakeSource($source)
    {
        return $this->createQueryBuilder('model')
            ->innerJoin('model.make', 'make')
            ->where('make.source = :source')
            ->setParameter('source', $source)
            ->getQuery()
            ->getResult();
    }

    public function findByMakeId($makeId, $offset = 0, $limit = 100)
    {
        return $this->createQueryBuilder('model')
            ->innerJoin('model.make', 'make')
            ->where('make.id = :makeId')
            ->andWhere('model.active = :active')
            ->setParameter('makeId', $makeId)
            ->setParameter('active', true)
            ->orderBy('model.name')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
