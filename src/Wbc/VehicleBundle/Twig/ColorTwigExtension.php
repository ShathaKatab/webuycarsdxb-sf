<?php

namespace Wbc\VehicleBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;
use Wbc\VehicleBundle\Form\ColorType;

/**
 * Class ColorTwigExtension.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.vehicle.twig.color_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class ColorTwigExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('getColors', [$this, 'getColors'])];
    }

    /**
     * @return array
     */
    public function getColors()
    {
        return ColorType::getColors();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'getColors';
    }
}
