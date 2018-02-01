<?php

namespace Wbc\BlogBundle\Twig;

use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Wbc\BlogBundle\Entity\Category;

/**
 * Class CategoryTwigExtension.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.blog.twig.category_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class CategoryTwigExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * BranchTwigExtension Constructor.
     *
     * @DI\InjectParams({
     *  "entityManager" = @DI\Inject("doctrine.orm.default_entity_manager")
     * })
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('getCategories', [$this, 'getCategories'])];
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Category::class, 'c')
            ->innerJoin('c.posts', 'p', 'WITH', 'p.enabled = :enabled')
            ->orderBy('c.name', 'ASC')
            ->setParameter(':enabled', true, \PDO::PARAM_BOOL)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'getCategories';
    }
}
