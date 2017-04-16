<?php

namespace Wbc\VehicleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TransmissionType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class TransmissionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Vehicle Transmission',
            'choices' => self::getTransmissions(),
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
        return 'wbc_vehicle_transmission_type';
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
    public static function getTransmissions()
    {
        return ['automatic' => 'Automatic', 'manual' => 'Manual', 'other' => 'Other',];
    }
}
