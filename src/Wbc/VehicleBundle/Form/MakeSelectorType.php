<?php

namespace Wbc\VehicleBundle\Form;

use Doctrine\Common\Persistence\ObjectManager;
use Wbc\VehicleBundle\Form\DataTransformer\MakeToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class MakeSelectorType.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @DI\FormType()
 * @DI\Tag(name="form.type")
 */
class MakeSelectorType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * Constructor.
     *
     * @param ObjectManager $manager
     *
     * @DI\InjectParams({
     *    "manager" = @DI\Inject("doctrine.orm.default_entity_manager")
     * })
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new MakeToIdTransformer($this->manager));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'The selected Vehicle Make does not exist',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }
}
