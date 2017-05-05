<?php

namespace Wbc\UtilityBundle\Twig;

use Doctrine\ORM\EntityRepository;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class BranchTwigExtension.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.branch.twig.branch_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class BranchTwigExtension extends \Twig_Extension
{
    /**
     * @var Markdown
     */
    private $parser;

    /**
     * BranchTwigExtension Constructor.
     *
     * @DI\InjectParams({
     *  "repository" = @DI\Inject("wbc.branch.branch_repository")
     * })
     *
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('getBranches', [$this, 'getBranches'])];
    }

    /**
     * @return array
     */
    public function getBranches()
    {
        return $this->repository->findAll();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'getBranches';
    }
}
