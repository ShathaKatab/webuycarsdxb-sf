<?php

namespace Wbc\VehicleBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Wbc\VehicleBundle\Entity\ModelType;

/**
 * Class ModelTypeToIdTransformer.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ModelTypeToIdTransformer implements DataTransformerInterface
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
     * Transforms an object (Model) to a string (id).
     *
     * @param ModelType|null $modelType
     *
     * @return string
     */
    public function transform($modelType)
    {
        if (null == $modelType) {
            return '';
        }

        return $modelType->getId();
    }

    /**
     * Transforms a string (id) to an object (ModelType).
     *
     * @param string $modelTypeId
     *
     * @return ModelType|null
     *
     * @throws TransformationFailedException if object (Model) is not found.
     */
    public function reverseTransform($modelTypeId)
    {
        if (!$modelTypeId) {
            return;
        }

        $modelType = $this->manager->getRepository('WbcVehicleBundle:ModelType')->find($modelTypeId);

        if ($modelType == null) {
            throw new TransformationFailedException(sprintf('Vehicle Model Type with id "%s" does not exist!', $modelTypeId));
        }

        return $modelType;
    }
}
