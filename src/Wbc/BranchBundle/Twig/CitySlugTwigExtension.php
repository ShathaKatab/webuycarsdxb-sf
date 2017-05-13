<?php

namespace Wbc\BranchBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class CitySlugTwigExtension.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.utility.twig.city_slug_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class CitySlugTwigExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('citySlug', [$this, 'citySlug'])];
    }

    /**
     * @param $slug
     *
     * @return string
     */
    public function citySlug($slug)
    {
        $cities = [
            'dubai' => 'Dubai',
            'abu-dhabi' => 'Abu Dhabi',
            'al-ain' => 'Al Ain',
            'sharjah' => 'Sharjah',
            'ajman' => 'Ajman',
            'umm-al-quwain' => 'Umm Al Quwain',
            'ras-al-khaimah' => 'Ras Al Khaimah',
        ];

        if (isset($cities[$slug])) {
            return $cities[$slug];
        }

        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'citySlug';
    }
}
