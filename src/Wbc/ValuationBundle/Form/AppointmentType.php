<?php

namespace Wbc\ValuationBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wbc\BranchBundle\Form\BranchSelectorType;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Entity\Timing;
use Wbc\ValuationBundle\Entity\Valuation;

/**
 * Class AppointmentType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class AppointmentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('valuation', EntityType::class, ['class' => Valuation::class, 'label' => 'Valuation'])
            ->add('branch', BranchSelectorType::class, ['label' => 'Branch'])
            ->add('branchTiming', EntityType::class, ['class' => Timing::class, 'label' => 'Branch Timing'])
            ->add('dateBooked', DateType::class, ['format' => DateType::HTML5_FORMAT, 'label' => 'Date'])
            ->add('emailAddress', EmailType::class, ['label' => 'Email Address'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
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
