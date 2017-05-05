<?php

namespace Wbc\UtilityBundle\ORM;

use Doctrine\ORM\QueryBuilder;

/**
 * Class EntityRepository.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class EntityRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return array
     */
    protected function getResults(QueryBuilder $queryBuilder)
    {
        $maxResults = $queryBuilder->getMaxResults();

        if ($maxResults) {
            ++$maxResults;
        }

        $queryBuilder->setMaxResults($maxResults);

        return $queryBuilder->getQuery()->execute();
    }
}
