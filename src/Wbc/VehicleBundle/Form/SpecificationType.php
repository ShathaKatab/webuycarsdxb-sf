<?php

namespace Wbc\VehicleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SpecificationType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class SpecificationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Vehicle Regional Specifications',
            'choices' => self::getSpecifications(),
            'placeholder' => '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wbc_vehicle_specification_type';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @return array
     */
    public static function getSpecifications()
    {
        return [
            'gcc' => 'GCC Specs',
            'usa' => 'US Specs',
            'jpn' => 'Japanese Specs',
            'euro' => 'European Specs',
            'other' => 'Other',
        ];
    }
}
