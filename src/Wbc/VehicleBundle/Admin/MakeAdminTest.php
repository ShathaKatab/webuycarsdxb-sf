<?php

namespace Wbc\VehicleBundle\Admin;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Float_;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpKernel\Tests\Controller;

/**
 * Class MakeAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class MakeAdminTest extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $form)
    {
        $form->add('price', TextType::class, ['required' => false, 'label' => 'price']);
        $form->add('updated', CheckboxType::class, ['required' => true, 'label' => 'Update true price']);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id')
            ->add('title')
            ->add('make')
            ->add('model')
            ->add('price')
            ->add('body_condition')
            ->add('body_type')
            ->add('year')
            ->add('_action', 'actions', [
                'actions' => ['edit' => [], 'delete' => [],],]);
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('make')->add('model')->add('year');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show->add('id')
            ->add('title')
            ->add('make')
            ->add('model')
            ->add('price')
            ->add('body_condition')
            ->add('body_type')
            ->add('year');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept([ 'edit', 'show', 'list']);
    }
}
