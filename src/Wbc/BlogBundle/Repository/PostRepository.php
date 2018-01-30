<?php

namespace Wbc\BlogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Wbc\BlogBundle\Entity\Post;

/**
 * Class PostRepository.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class PostRepository extends EntityRepository
{
    /**
     * @param array $slug
     *
     * @return Post
     */
    public function findOneBySlugAndEnabled($slug)
    {
        return $this->createQueryBuilder('p')
            ->where('p.enabled = :enabled')
            ->andWhere('p.slug = :slug')
            ->andWhere('p.publicationDateStart <= :nowDateTime')
            ->setParameter(':nowDateTime', (new \DateTime())->format('Y-m-d H:i:s'), \PDO::PARAM_STR)
            ->setParameter(':slug', $slug['slug'], \PDO::PARAM_STR)
            ->setParameter(':enabled', true, \PDO::PARAM_BOOL)
            ->getQuery()
            ->getSingleResult();
    }
}
