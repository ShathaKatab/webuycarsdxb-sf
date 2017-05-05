<?php

namespace Wbc\UtilityBundle\Request\Options;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ListOptions.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ListOptions extends AbstractOptions
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'page' => 1,
            'per_page' => 30,
        ]);

        $resolver->setAllowedTypes('page', array('null', 'int', 'string'));
        $resolver->setAllowedTypes('per_page', array('null', 'int', 'string'));
    }

    /**
     * SQL kind of offset, 0-based.
     *
     * @return int|mixed
     */
    public function getOffset()
    {
        $page = $this->get('page');
        $per_page = $this->get('per_page');

        if ($page < 1) {
            return 0;
        }

        return ($page - 1) * $per_page;
    }

    /**
     * SQL helper method.
     *
     * @return mixed
     */
    public function getLimit()
    {
        return $this->get('per_page');
    }
}
