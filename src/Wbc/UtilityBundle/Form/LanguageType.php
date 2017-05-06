<?php

namespace Wbc\UtilityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class LanguageType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\FormType("wbc.utility.form.language")
 * @DI\Tag(name="form.type", attributes={"alias": "wbc_utility_form_language"})
 */
class LanguageType extends AbstractType
{
    /**
     * @var array
     */
    protected $choices;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->choices = ['en' => 'English', 'ar' => 'Arabic'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['choices' => $this->choices, 'empty_value' => '-- Choose your Preferred Language --']);
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
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wbc_utility_form_language';
    }
}
