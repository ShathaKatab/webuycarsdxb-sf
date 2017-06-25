<?php

namespace Wbc\ValuationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wbc\VehicleBundle\Form\MakeType;
use Wbc\VehicleBundle\Form\ModelSelectorType;
use Wbc\VehicleBundle\Form\ModelYearType;

/**
 * Class ValuationStepOneType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ValuationStepOneType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('vehicleMake', MakeType::class, [
            'invalid_message' => 'Vehicle Make is not valid',
            'label' => 'Vehicle Make',
        ])
            ->add('vehicleModel', ModelSelectorType::class, [
                'invalid_message' => 'Vehicle Model is not valid',
                'label' => 'Vehicle Model',
            ])
            ->add('vehicleYear', ModelYearType::class, [
                'invalid_message' => 'Vehicle Year is not valid',
                'label' => 'Vehicle Year',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return '';
    }
}
