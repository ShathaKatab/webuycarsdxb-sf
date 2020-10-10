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
 * Class HolidayAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class HolidayAdmin extends AbstractAdmin
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
            ->add('from', 'Symfony\Component\Form\Extension\Core\Type\DateType', ['widget' => 'single_text'])
            ->add('to', 'Symfony\Component\Form\Extension\Core\Type\DateType', ['widget' => 'single_text'])
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
            ->add('branch.name')
            ->add('from', 'Symfony\Component\Form\Extension\Core\Type\DateType')
            ->add('to', 'Symfony\Component\Form\Extension\Core\Type\DateType')
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
            ->add('from')
            ->add('to')
        ;
    }
}
