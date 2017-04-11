<?php

namespace Wbc\VehicleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ModelYearType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ModelYearType extends AbstractType
{
    protected $choices;

    public function __construct()
    {
        $years = range(date('Y') + 1, date('Y') - 90);
        $this->choices = array_combine($years, $years);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Vehicle Model Year',
            'choices' => $this->choices,
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
        return 'wbc_vehicle_model_year_type';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
