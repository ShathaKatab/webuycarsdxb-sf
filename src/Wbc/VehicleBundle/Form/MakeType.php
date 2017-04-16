<?php

namespace Wbc\VehicleBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Wbc\UtilityBundle\ORM\EntityRepository;
use Wbc\VehicleBundle\Entity\Make;
use Symfony\Component\OptionsResolver\OptionsResolver;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class MakeSelectorType.
 *
 * @DI\FormType()
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class MakeType extends EntityType
{
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * MakeType Constructor.
     *
     * @DI\InjectParams({
     *  "managerRegistry" = @DI\Inject("doctrine")
     * })
     *
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        parent::__construct($managerRegistry);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'placeholder' => '',
            'query_builder' => function (EntityRepository $entityRepository) {
                $queryBuilder = $entityRepository->createQueryBuilder('m')
                    ->where('m.active = :active')
                    ->orderBy('m.name', 'ASC');
                $queryBuilder->setParameter('active', true);

                return $queryBuilder;
            },
            'class' => Make::class,
            'property' => 'name',
            'invalid_message' => 'The selected Vehicle Make does not exist',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return EntityType::class;
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
        return 'wbc_make_selector_type';
    }
}
