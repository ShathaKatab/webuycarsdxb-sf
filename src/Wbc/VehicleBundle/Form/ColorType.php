<?php

namespace Wbc\VehicleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ColorType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ColorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Vehicle Body Color',
            'choices' => [
                'white' => 'White',
                'silver' => 'Silver',
                'black' => 'Black',
                'grey' => 'Grey',
                'blue' => 'Blue',
                'red' => 'Red',
                'brown' => 'Brown',
                'green' => 'Green',
                'other' => 'Other',
            ],
            'placeholder' => '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wbc_vehicle_color_type';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
