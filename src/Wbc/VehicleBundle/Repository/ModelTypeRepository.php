<?php

namespace Wbc\VehicleBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class ModelTypeRepository.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ModelTypeRepository extends EntityRepository
{
    public function findAllBy($makeId = null, $modelId = null, $offset = 0, $limit = 100)
    {
        $queryBuilder = $this->createQueryBuilder('modelType')
            ->innerJoin('modelType.model', 'model')
            ->innerJoin('model.make', 'make');

        if ($modelId) {
            $queryBuilder->where('model.id = :modelId')
                ->setParameter('modelId', $modelId);
        } elseif ($makeId) {
            $queryBuilder->where('make.id = :makeId')
                ->setParameter('makeId', $makeId);
        }

        return $queryBuilder->addOrderBy('model.name')
            ->addOrderBy('modelType.engine')
            ->addGroupBy('make.id')
            ->addGroupBy('model.id')
            ->addGroupBy('modelType.engine')
            ->addGroupBy('modelType.bodyType')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
