<?php

namespace Wbc\UtilityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use JMS\DiExtraBundle\Annotation as DI;
use Wbc\UtilityBundle\Validator\Constraints\PhoneNumber;

/**
 * Class PhoneNumberType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\FormType("wbc.utility.form.phone_number")
 * @DI\Tag(name="form.type", attributes={"alias": "wbc_utility_form_phone_number"})
 */
class PhoneNumberType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'constraints' => [
                new PhoneNumber(['message' => 'Invalid phone number']),
            ],
        ]);
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\TextType';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wbc_utility_form_phone_number';
    }
}
