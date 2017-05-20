<?php

namespace Wbc\VehicleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ModelYearType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ModelYearType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Vehicle Model Year',
            'choices' => self::getYears(),
            'placeholder' => '',
            'choices_as_values' => true,
            'choice_label' => function ($value, $key, $index) {
                if ($value) {
                    return $value;
                }
            },
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
        return 'wbc_vehicle_model_year_type';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * Gets Years.
     *
     * @return array
     */
    public static function getYears()
    {
        return range(date('Y') + 1, date('Y') - 90);
    }
}
