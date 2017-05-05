<?php

namespace Wbc\UtilityBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/*
 * Class GenderType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class GenderType extends AbstractType
{
    const GENDER_MALE = 'm';
    const GENDER_FEMALE = 'f';

    /**
     * @var array
     */
    protected $choices;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->choices = [
            self::GENDER_FEMALE => 'Female',
            self::GENDER_MALE => 'Male',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['choices' => $this->choices, 'empty_value' => '-- Choose your Gender --']);
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
        return 'wbc_utility_gender';
    }
}
