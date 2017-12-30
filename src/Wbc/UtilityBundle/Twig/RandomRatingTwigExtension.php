<?php

namespace Wbc\UtilityBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class RandomRatingTwigExtension.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.utility.twig.random_rating_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class RandomRatingTwigExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [new \Twig_SimpleFunction('randomRating', [$this, 'getRating'])];
    }

    /**
     * @param int   $min
     * @param int   $max
     * @param mixed $stepper
     *
     * @return float
     */
    public function getRating($min = 4, $max = 5, $stepper = .5)
    {
        $ratings = range($min, $max, $stepper);

        return $ratings[array_rand($ratings)];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'random_rating_extension';
    }
}
