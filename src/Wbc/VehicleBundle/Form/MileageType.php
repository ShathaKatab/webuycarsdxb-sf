<?php

namespace Wbc\VehicleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MileageType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class MileageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Vehicle Mileage',
            'choices' => array_keys(self::getMileages()),
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
        return 'wbc_vehicle_mileage_type';
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
    public static function getMileages()
    {
        $listOfMileages = array_merge([5000], range(10000, 250000, 10000));
        $mileages = [];
        array_map(function ($value) use (&$mileages) {
            return $mileages[$value] = sprintf('Up to %s', number_format($value));
        }, $listOfMileages);

        $mileages[260000] = 'Other';

        return $mileages;
    }
}
