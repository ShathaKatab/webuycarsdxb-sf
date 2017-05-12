<?php

namespace Wbc\ValuationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wbc\ValuationBundle\Entity\Valuation;
use Wbc\VehicleBundle\Form\ColorType;
use Wbc\VehicleBundle\Form\ConditionType;
use Wbc\VehicleBundle\Form\MileageType;
use Wbc\VehicleBundle\Form\ModelSelectorType;
use Wbc\VehicleBundle\Form\ModelTypeSelectorType;
use Wbc\VehicleBundle\Form\ModelYearType;
use Wbc\UtilityBundle\Form\MobileNumberType;

/**
 * Class ValuationType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ValuationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('vehicleModel', ModelSelectorType::class, [
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
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('emailAddress', EmailType::class, ['label' => 'Email Address'])
            ->add('mobileNumber', MobileNumberType::class, ['label' => 'Mobile Number'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Valuation::class,
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
