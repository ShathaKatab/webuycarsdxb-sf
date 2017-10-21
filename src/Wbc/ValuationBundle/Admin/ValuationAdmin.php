<?php

namespace Wbc\ValuationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\VehicleBundle\Entity\Make;
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

    /**
     * {@inheritdoc}
     */
    public function getExportFields()
    {
        return ['name', 'mobileNumber', 'emailAddress', 'vehicleMake', 'vehicleModel', 'vehicleMileage', 'priceOnline', 'hasAppointment', 'createdAt'];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSourceIterator()
    {
        $iterator = parent::getDataSourceIterator();
        $iterator->setDateTimeFormat('M d, Y');

        return $iterator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $now = new \DateTime();
        $datagridMapper->add('name')
            ->add('mobileNumber')
            ->add('vehicleMake', 'doctrine_orm_callback', ['callback' => function ($queryBuilder, $alias, $field, $value) {
                if (!$value || ($value && !$value['value'] instanceof Make)) {
                    return false;
                }
                $queryBuilder->innerJoin($alias.'.vehicleModel', 'vehicleModel')->andWhere('vehicleModel.make = :make')->setParameter(':make', $value['value']);

                return true;
            }, 'field_type' => 'entity', 'field_options' => ['class' => Make::class]])
            ->add('vehicleModel')
            ->add('vehicleYear')
            ->add('hasAppointment', 'doctrine_orm_callback', [
                'label' => 'Has Appointment',
                'callback' => function ($queryBuilder, $alias, $field, $value) use ($now) {
                    if ($value['value'] === null) {
                        return;
                    }

                    $theValue = (bool) ($value['value']);

                    if ($theValue === true) {
                        $queryBuilder->innerJoin($alias.'.appointment', 'appointment')
                            ->andWhere('appointment IS NOT NULL');
                    } elseif ($theValue === false) {
                        $queryBuilder->leftJoin($alias.'.appointment', 'appointment')
                            ->andWhere('appointment IS NULL');
                    }

                    return true;
                },
                'field_type' => 'choice',
                'field_options' => [
                    'choices' => [
                        1 => 'yes',
                        0 => 'no',
                    ],
                ],
            ])
            ->add('createdAt', 'doctrine_orm_date_range', [
                'label' => 'Date Created At',
                'field_type' => 'sonata_type_date_range_picker',
                'start_options' => [
                    'years' => range($now->format('Y'), (int) ($now->format('Y')) + 1),
                    'dp_min_date' => (new \DateTime('-1 month'))->format('d/M/Y'),
                    'dp_max_date' => (new \DateTime('+1 month'))->format('d/M/Y'),
                    'dp_default_date' => $now->format('m/d/Y'), ],
                'end_options' => [
                    'years' => range($now->format('Y'), (int) ($now->format('Y')) + 1),
                    'dp_min_date' => (new \DateTime('-1 month'))->format('d/M/Y'),
                    'dp_max_date' => (new \DateTime('+1 month'))->format('d/M/Y'),
                    'dp_default_date' => $now->format('m/d/Y'),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name')
            ->add('mobileNumber', null, ['label' => 'Mobile'])
            ->add('vehicleModel.make')
            ->add('vehicleModel.name')
            ->add('vehicleYear')
            ->add('vehicleMileage', null, ['label' => 'Mileage (Kms)'])
            ->add('priceOnline', 'currency', ['currency' => 'AED'])
            ->add('hasAppointment', 'boolean')
            ->add('createdAt', null, ['label' => 'Created'])
            ->add('_action', 'actions', ['actions' => [
                'show' => [],
                'edit' => [],
                'appointment' => [
                    'template' => 'WbcValuationBundle:Admin/CRUD:list__action_appointment.html.twig',
                ],
            ]]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show->add('id')
            ->add('name')
            ->add('mobileNumber', null, ['label' => 'Mobile'])
            ->add('emailAddress')
            ->add('vehicleModel.make')
            ->add('vehicleModel.name')
            ->add('vehicleModelType.name')
            ->add('vehicleYear')
            ->add('vehicleMileage', null, ['label' => 'Mileage (Kms)'])
            ->add('vehicleBodyCondition', 'choice', ['choices' => ConditionType::getConditions()])
            ->add('vehicleColor', 'choice', ['choices' => ColorType::getColors()])
            ->add('priceOnline', 'currency', ['currency' => 'AED'])
            ->add('createdAt', null, ['label' => 'Created']);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create')->remove('delete')->remove('edit')->add('generateAppointment', $this->getRouterIdParameter().'/generateAppointment');

        $container = $this->getConfigurationPool()->getContainer();

        if ($container->get('security.token_storage')->getToken()) {
            $authorizationChecker = $container->get('security.authorization_checker');

            if (!$authorizationChecker->isGranted('ROLE_VALUATION_ADMIN')) {
                $collection->remove('delete')->remove('export');
            }
        }
    }
}
