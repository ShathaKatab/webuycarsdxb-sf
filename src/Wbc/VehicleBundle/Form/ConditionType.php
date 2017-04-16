<?php

namespace Wbc\VehicleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ConditionType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ConditionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Vehicle Condition',
            'choices' => self::getConditions(),
            'placeholder' => '', ]);
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
        return 'wbc_vehicle_condition_type';
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
    public static function getConditions()
    {
        return ['fair' => 'Fair', 'good' => 'Good', 'excellent' => 'Excellent', 'other' => 'Other',];
    }
}
