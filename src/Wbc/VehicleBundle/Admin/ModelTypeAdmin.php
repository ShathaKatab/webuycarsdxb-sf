<?php

namespace Wbc\VehicleBundle\Admin;

use Proxies\__CG__\Wbc\VehicleBundle\Entity\Model;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Wbc\VehicleBundle\Form\ModelYearType;
use Wbc\VehicleBundle\Form\TransmissionType;

/**
 * Class ModelTypeAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ModelTypeAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form->add('trim', TextType::class, ['label' => 'Trim Name'])
            ->add('model')
            ->add('engine', IntegerType::class, ['help' => 'e.g. 2500 for a 2.5 litre engine'])
            ->add('cylinders', IntegerType::class)
            ->add('transmission', TransmissionType::class)
            ->add('seats', IntegerType::class)
            ->add('isGcc', CheckboxType::class, ['required' => false])
            ->add('years', ModelYearType::class, ['multiple' => true])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id')
            ->add('trim')
            ->add('model')
            ->add('engine')
            ->add('cylinders')
            ->add('transmission')
            ->add('seats')
            ->add('isGcc')
            ->add('flattenedYears', null, ['label' => 'Years'])
            ->add('createdAt')
            ->add('updatedAt')
            ->add('_action', 'actions', [
                'actions' => ['show' => [], 'edit' => [], 'delete' => []], ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('trim')->add('model')->add('isGcc');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show->add('id')
            ->add('trim')
            ->add('model')
            ->add('engine')
            ->add('cylinders')
            ->add('transmission')
            ->add('seats')
            ->add('isGcc')
            ->add('flattenedYears', null, ['label' => 'Years'])
            ->add('createdAt')
            ->add('updatedAt');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['create', 'edit', 'show', 'list']);
    }
}
