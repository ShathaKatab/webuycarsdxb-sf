<?php

namespace Wbc\BranchBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Wbc\BranchBundle\Entity\Branch;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class BranchToSlugTransformer.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class BranchToSlugTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * Constructor.
     *
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (Branch) to a string (slug).
     *
     * @param Branch|null $branch
     *
     * @return string
     */
    public function transform($branch)
    {
        if (null == $branch) {
            return '';
        }

        return $branch->getSlug();
    }

    /**
     * Transforms a string (slug) to an object (Branch).
     *
     * @param string $branchSlug
     *
     * @return Branch|null
     *
     * @throws TransformationFailedException if object (Valuation) is not found.
     */
    public function reverseTransform($branchSlug)
    {
        if (!$branchSlug) {
            return;
        }

        $valuation = $this->manager->getRepository('WbcBranchBundle:Branch')->findOneBy(['slug' => $branchSlug]);

        if ($valuation == null) {
            throw new TransformationFailedException(sprintf('Branch with id "%s" does not exist!', $branchSlug));
        }

        return $valuation;
    }
}
