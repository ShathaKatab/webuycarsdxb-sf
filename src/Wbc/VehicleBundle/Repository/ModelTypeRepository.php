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
    /**
     * @param int $makeId
     * @param int $modelId
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function findAllBy($makeId = null, $modelId = null, $offset = 0, $limit = 300)
    {
        $queryBuilder = $this->createQueryBuilder('modelType')
            ->innerJoin('modelType.model', 'model')
            ->innerJoin('model.make', 'make');

        if ($modelId) {
            $queryBuilder->where('model.id = :modelId')
                ->setParameter('modelId', $modelId)
                ->addGroupBy('model.id');
        } elseif ($makeId) {
            $queryBuilder->where('make.id = :makeId')
                ->setParameter('makeId', $makeId)
                ->addGroupBy('make.id');
        }

        return $queryBuilder->addOrderBy('model.name')
            ->addGroupBy('modelType.id')
            ->addOrderBy('modelType.engine')
            ->addGroupBy('modelType.engine')
            ->addGroupBy('modelType.bodyType')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
