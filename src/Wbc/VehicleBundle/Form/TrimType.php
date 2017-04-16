<?php

namespace Wbc\VehicleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TrimType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class TrimType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Vehicle Regional Specifications',
            'choices' => self::getTrims(),
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
        return 'wbc_vehicle_trim_type';
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
    public static function getTrims()
    {
        return ['base' => 'Base option', 'mid' => 'Mid option', 'full' => 'Full option', 'other' => 'Other'];
    }
}
