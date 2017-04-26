<?php

namespace Wbc\CrawlerBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class ClassifiedsModelRepository.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ClassifiedsModelRepository extends EntityRepository
{
    /**
     * @param $source
     * @return array
     */
    public function findByMakeSource($source)
    {
        return $this->createQueryBuilder('model')
            ->innerJoin('model.make', 'make')
            ->where('make.source = :source')
            ->setParameter('source', $source)
            ->orderBy('model.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $makeId
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function findByMakeId($makeId, $offset = 0, $limit = 100)
    {
        return $this->createQueryBuilder('model')
            ->innerJoin('model.make', 'make')
            ->where('make.id = :makeId')
            ->andWhere('model.isActive = :isActive')
            ->setParameter('makeId', $makeId)
            ->setParameter('isActive', true)
            ->orderBy('model.name')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
