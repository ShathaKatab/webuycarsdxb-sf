<?php

namespace Wbc\BranchBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class TimingAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class TimingAdmin extends Admin
{
    /**
     * Fields to be shown on create/edit forms.
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('branch', EntityType::class, [
            'class' => 'Wbc\BranchBundle\Entity\Branch',
            'property' => 'name',
            'placeholder' => '-- Select a Branch --',
        ])
            ->add('day', ChoiceType::class, [
                'choices' => [
                    6 => 'Saturday',
                    7 => 'Sunday',
                    1 => 'Monday',
                    2 => 'Tuesday',
                    3 => 'Wednesday',
                    4 => 'Thursday',
                    5 => 'Friday',
                ],
                'placeholder' => '-- Select a Day --',
            ])
            ->add('from', TextType::class, [
                'help' => 'e.g. 09:00',
            ])
            ->add('to', TextType::class, [
                'help' => 'e.g. 13:00',
            ])
            ->add('numberOfSlots', IntegerType::class);
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
        $listMapper->add('branch.name')
            ->add('day', 'choice', [
                'choices' => [
                    6 => 'Saturday',
                    7 => 'Sunday',
                    1 => 'Monday',
                    2 => 'Tuesday',
                    3 => 'Wednesday',
                    4 => 'Thursday',
                    5 => 'Friday',
                ],
            ])
            ->add('from')
            ->add('to')
            ->add('numberOfSlots', 'integer', ['editable' => true])
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
            ->add('day')
            ->add('from')
            ->add('to')
            ->add('numberOfSlots');
    }
}
