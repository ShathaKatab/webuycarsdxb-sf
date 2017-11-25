<?php

namespace Wbc\ValuationBundle\Form;

use Wbc\ValuationBundle\Entity\Valuation;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wbc\VehicleBundle\Form\ColorType;
use Wbc\VehicleBundle\Form\ConditionType;
use Wbc\VehicleBundle\Form\MileageType;
use Wbc\VehicleBundle\Form\ModelSelectorType;
use Wbc\VehicleBundle\Form\ModelTypeSelectorType;
use Wbc\VehicleBundle\Form\ModelYearType;
use Wbc\UtilityBundle\Form\MobileNumberType;

/**
 * Class ValuationStepTwoType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ValuationStepTwoType extends ValuationStepOneType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('vehicleMake')
            ->add('vehicleModel', ModelSelectorType::class, [
                'invalid_message' => 'Vehicle Model is not valid',
                'label' => 'Vehicle Model',
            ])
            ->add('vehicleYear', ModelYearType::class, [
                'invalid_message' => 'Vehicle Year is not valid',
                'label' => 'Vehicle Year',
            ])
            ->add('vehicleModelType', ModelTypeSelectorType::class, [
                'invalid_message' => 'Vehicle Trim is not valid',
                'label' => 'Vehicle Trim',
            ])
            ->add('vehicleMileage', MileageType::class, ['label' => 'Vehicle Mileage'])
            ->add('vehicleBodyCondition', ConditionType::class, ['label' => 'Vehicle Body Condition'])
            ->add('vehicleColor', ColorType::class, ['label' => 'Vehicle Color'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', Valuation::class);
    }
}
