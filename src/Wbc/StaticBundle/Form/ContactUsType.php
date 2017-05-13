<?php

namespace Wbc\StaticBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Wbc\UtilityBundle\Form\PhoneNumberType;
use Wbc\UtilityBundle\Validator\Constraints\PhoneNumber;

/**
 * Class ContactUsType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ContactUsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['constraints' => new NotBlank()])
            ->add('emailAddress', EmailType::class, ['constraints' => [new NotBlank(), new Email()]])
            ->add('phoneNumber', PhoneNumberType::class, ['constraints' => [new NotBlank(), new PhoneNumber()]])
            ->add('message', TextareaType::class, ['constraints' => new NotBlank()])
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
