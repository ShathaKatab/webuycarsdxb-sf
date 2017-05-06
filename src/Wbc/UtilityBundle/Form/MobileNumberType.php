<?php

namespace Wbc\UtilityBundle\Form;

use Wbc\UtilityBundle\Validator\Constraints\MobileNumber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class MobileNumberType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\FormType("wbc.utility.form.mobile_number")
 * @DI\Tag(name="form.type", attributes={"alias": "wbc_utility_form_mobile_number"})
 */
class MobileNumberType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'constraints' => [
                new NotBlank(['message' => 'fos_user.mobileNumber.blank']),
                new MobileNumber(['message' => 'fos_user.mobileNumber.match']),
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
        return 'wbc_utility_form_mobile_number';
    }
}
