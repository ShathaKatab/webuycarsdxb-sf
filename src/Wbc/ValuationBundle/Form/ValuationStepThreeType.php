<?php

namespace Wbc\ValuationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wbc\UtilityBundle\Form\MobileNumberType;
use Wbc\ValuationBundle\Entity\Valuation;

/**
 * Class ValuationStepThreeType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ValuationStepThreeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['label' => 'Name'])
            ->add('mobileNumber', MobileNumberType::class, ['label' => 'Mobile number (You will receive an SMS)'])
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
            'validation_groups' => ['valuation-step-3'],
        ]);
    }
}
