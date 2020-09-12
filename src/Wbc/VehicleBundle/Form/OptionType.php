<?php

declare(strict_types=1);

namespace Wbc\VehicleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class OptionType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class OptionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Vehicle Options',
            'choices' => array_keys(self::getOptions()),
            'placeholder' => ''
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix()
    {
        return 'wbc_vehicle_option_type';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @return array
     */
    public static function getOptions()
    {
        return ['base' => 'Base', 'mid' => 'Mid', 'full' => 'Full'];
    }
}
