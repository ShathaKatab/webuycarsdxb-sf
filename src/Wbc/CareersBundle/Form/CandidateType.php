<?php

declare(strict_types=1);

namespace Wbc\CareersBundle\Form;

use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wbc\CareersBundle\Entity\Candidate;
use Wbc\CareersBundle\Entity\Role;
use Wbc\UtilityBundle\Form\MobileNumberType;

/**
 * Class CandidateType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class CandidateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('emailAddress', EmailType::class)
            ->add('mobileNumber', MobileNumberType::class)
            ->add('currentRole', TextType::class)
            ->add('coverLetter', TextareaType::class)
            ->add('uploadedFile', FileType::class)
            ->add('role', EntityType::class, ['class' => Role::class])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Candidate::class, 'csrf_protection' => false]);
    }
}
