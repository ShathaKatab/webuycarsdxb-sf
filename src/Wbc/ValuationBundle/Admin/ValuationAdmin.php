<?php

namespace Wbc\ValuationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Wbc\VehicleBundle\Form\ColorType;
use Wbc\VehicleBundle\Form\ConditionType;

/**
 * Class ValuationAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ValuationAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_per_page' => 25,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name')
            ->add('mobileNumber', null, ['label' => 'Mobile'])
            ->add('vehicleModel.make')
            ->add('vehicleModel.name')
            ->add('vehicleYear')
            ->add('vehicleMileage', null, ['label' => 'Mileage (Kms)'])
            ->add('priceOnline', 'currency', ['currency' => 'AED'])
            ->add('createdAt', null, ['label' => 'Created'])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show->add('id')
            ->add('name')
            ->add('mobileNumber', null, ['label' => 'Mobile'])
            ->add('emailAddress')
            ->add('vehicleModel.make')
            ->add('vehicleModel.name')
            ->add('vehicleYear')
            ->add('vehicleMileage', null, ['label' => 'Mileage (Kms)'])
            ->add('vehicleBodyCondition', 'choice', ['choices' => ConditionType::getConditions()])
            ->add('vehicleColor', 'choice', ['choices' => ColorType::getColors()])
            ->add('priceOnline', 'currency', ['currency' => 'AED'])
            ->add('createdAt', null, ['label' => 'Created']);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create')->remove('delete')->remove('edit');
    }
}
