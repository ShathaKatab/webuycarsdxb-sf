<?php

namespace Wbc\BranchBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ObjectType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;

/**
 * Class BranchType.
 *
 * @DI\Service()
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class BranchType extends ObjectType
{
    const TYPE_NAME = 'branch_object';

    /**
     * @var Serializer
     *
     * @DI\Inject("serializer")
     */
    public $serializer;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return static::TYPE_NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value) {
            return $this->serializer->deserialize($value, '\Wbc\BranchBundle\Entity\Branch', 'json');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value) {
            return $this->serializer->serialize($value, 'json');
        }
    }
}
