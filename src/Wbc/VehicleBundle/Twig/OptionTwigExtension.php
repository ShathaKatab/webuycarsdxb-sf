<?php

declare(strict_types=1);

namespace Wbc\VehicleBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;
use Wbc\VehicleBundle\Form\OptionType;

/**
 * Class OptionTwigExtension .
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.vehicle.twig.option_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class OptionTwigExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('getOptions', [$this, 'getOptions'])];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return OptionType::getOptions();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'getOptions';
    }
}
