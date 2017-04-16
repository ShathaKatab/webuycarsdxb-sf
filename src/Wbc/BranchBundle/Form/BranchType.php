<?php

namespace Wbc\BranchBundle\Form;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wbc\BranchBundle\Entity\Branch;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class BranchType.
 *
 * @DI\FormType()
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class BranchType extends EntityType
{
    /**
     * BranchType Constructor.
     *
     * @DI\InjectParams({
     *  "managerRegistry" = @DI\Inject("doctrine")
     * })
     *
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'Branch',
            'placeholder' => '',
            'query_builder' => function (EntityRepository $entityRepository) {
                $queryBuilder = $entityRepository->createQueryBuilder('b')->where('b.active = :active');
                $queryBuilder->setParameter('active', true);

                return $queryBuilder;
            },
            'property' => 'name',
            'class' => Branch::class,
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
        return 'wbc_branch_type';
    }
}
