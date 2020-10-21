<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Wbc\BranchBundle\Form\DayType;

/**
 * Class TimingAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class TimingAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_order' => 'DESC',
        '_sort_by'    => 'createdAt',
    ];

    /**
     * Fields to be shown on create/edit forms.
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('branch', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
            'class'       => 'Wbc\BranchBundle\Entity\Branch',
            'property'    => 'name',
            'placeholder' => '-- Select a Branch --',
        ])
            ->add('dayOfWeek', 'Wbc\BranchBundle\Form\DayType')
            ->add('fromString', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'help'  => 'e.g. 09:00',
                'label' => 'From Time',
            ])
            ->add('toString', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'help'  => 'e.g. 13:00',
                'label' => 'To Time',
            ])
        ;
    }

    /**
     * Fields to be shown on filter forms.
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('branch.name');
    }

    /**
     * Fields to be shown on lists.
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('dayOfWeek', 'choice', [
                'choices' => DayType::getDays(),
            ])
            ->add('branch.name')
            ->add('fromString', null, ['label' => 'From Time'])
            ->add('toString', null, ['label' => 'To Time'])
        ;
    }

    /**
     * Fields to be shown on show action.
     *
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('branch.name')
            ->add('dayOfWeek')
            ->add('fromString', null, ['label' => 'From Time'])
            ->add('toString', null, ['label' => 'From Time'])
        ;
    }
}
