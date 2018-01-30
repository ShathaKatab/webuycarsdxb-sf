<?php

namespace Wbc\BlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as CF;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Wbc\BlogBundle\Entity\Post;

/**
 * Class BlogController.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class BlogController extends Controller
{
    /**
     * @CF\Route("/blog/{page}", name="wbc_blog_list", defaults={"page"=1}, requirements={"page"="\d+"})
     * @CF\Route("/blog/category/{category}/{page}", name="wbc_blog_category_list", defaults={"page"=1}, requirements={"page"="\d+", "category"="[a-z0-9A-Z_\-]+"})
     * @CF\Method("GET")
     *
     * @param int    $page
     * @param string $category
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function listAction($page = 1, $category = null)
    {
        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $this->get('doctrine.orm.default_entity_manager');
        $queryBuilder = $entityManager->createQueryBuilder()->select('p')
            ->from(Post::class, 'p')
            ->where('p.enabled = :enabled')
            ->andWhere('p.publicationDateStart <= :nowDateTime')
            ->orderBy('p.publicationDateStart', 'DESC')
            ->setParameter(':nowDateTime', (new \DateTime())->format('Y-m-d H:i:s'), \PDO::PARAM_STR)
            ->setParameter(':enabled', true, \PDO::PARAM_BOOL);

        if ($category) {
            $queryBuilder->innerJoin('p.categories', 'c', 'WITH', 'c.slug = :categorySlug')
                ->setParameter(':categorySlug', $category, \PDO::PARAM_STR);
        }

        return $this->render('WbcBlogBundle:Blog:list.html.twig', [
            'pagination' => $this->get('knp_paginator')->paginate($queryBuilder->getQuery(), $page),
        ]);
    }

    /**
     * @CF\Route("/{slug}", name="wbc_blog_get", requirements={"slug"="[a-z0-9A-Z_\-]+"})
     * @CF\ParamConverter("post", class="WbcBlogBundle:Post", options={"repository_method" = "findOneBySlugAndEnabled", "mapping": {"slug"="slug"}})
     * @CF\Template()
     *
     * @param Post $post
     *
     * @return array
     */
    public function getAction(Post $post)
    {
        return ['article' => $post];
    }

    /**
     * @CF\Route("/blog/preview/{slug}", name="wbc_blog_preview", requirements={"slug"="[a-z0-9A-Z_\-]+"})
     * @CF\ParamConverter("post", class="WbcBlogBundle:Post", options={"repository_method" = "findOneBySlug", "mapping": {"slug"="slug"}})
     * @CF\Template("WbcBlogBundle:Blog:get.html.twig")
     * @CF\Security("has_role('ROLE_BLOG_EDITOR')")
     *
     * @param Post $post
     *
     * @return array
     */
    public function previewAction(Post $post)
    {
        return ['article' => $post];
    }
}
