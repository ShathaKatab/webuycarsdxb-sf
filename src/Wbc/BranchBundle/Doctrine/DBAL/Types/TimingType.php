<?php

namespace Wbc\BranchBundle\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ObjectType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Class TimingType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class TimingType extends ObjectType
{
    const TYPE_NAME = 'branch_timing_object';

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
            $value = (is_resource($value)) ? stream_get_contents($value) : $value;
            $val = unserialize($value);

            if ($val === false && $value !== 'b:0;') {
                throw ConversionException::conversionFailed($value, $this->getName());
            }

            return $val;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value) {
            return serialize($value);
        }
    }
}
