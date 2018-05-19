<?php

namespace Wbc\CareersBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;
use Wbc\CareersBundle\Entity\Role;

/**
 * Class DepartmentSlugTwigExtension.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\Service("wbc.careers.twig.department_slug_extension", public=false)
 * @DI\Tag(name="twig.extension")
 */
class DepartmentSlugTwigExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('departmentSlug', [$this, 'departmentSlug'])];
    }

    /**
     * @param $slug
     *
     * @return string
     */
    public function departmentSlug($slug)
    {
        $departments = Role::getDepartments();

        if (isset($departments[$slug])) {
            return $departments[$slug];
        }

        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'departmentSlug';
    }
}
