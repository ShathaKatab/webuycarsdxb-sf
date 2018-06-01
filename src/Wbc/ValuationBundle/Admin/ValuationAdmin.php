<?php

namespace Wbc\ValuationBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\UtilityBundle\AdminDateRange;
use Wbc\ValuationBundle\Entity\Valuation;
use Wbc\VehicleBundle\Entity\Make;
use Wbc\VehicleBundle\Form\ColorType;
use Wbc\VehicleBundle\Form\ConditionType;
use Wbc\VehicleBundle\Form\OptionType;

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
    public function getExportFormats()
    {
        return ['xls'];
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
            ->add('createdAt', 'doctrine_orm_date_range', AdminDateRange::getDoctrineOrmDateRange('Date Created At'))
            ->add('status', 'doctrine_orm_choice', [
                'field_options' => ['choices' => Valuation::getStatuses()],
                'field_type' => 'choice',
            ])
            ->add('source', 'doctrine_orm_choice', [
                'field_options' => ['choices' => Valuation::getSources()],
                'field_type' => 'choice',
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
            ->add('vehicleOption', 'choice', ['choices' => OptionType::getOptions()])
            ->add('priceOnline', 'currency', ['currency' => 'AED'])
            ->add('hasAppointment', 'boolean')
            ->add('status', 'choice', ['choices' => Valuation::getStatuses(), 'editable' => true])
            ->add('source', 'choice', ['choices' => Valuation::getSources(), 'editable' => true])
            ->add('createdAt', null, ['label' => 'Created'])
            ->add('_action', 'actions', ['actions' => [
                'show' => [],
                'edit' => [],
                'appointment' => [
                    'template' => 'WbcValuationBundle:Admin/CRUD:list__action_appointment.html.twig',
                ],
            ]]);
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form->add('notes')
            ->add('reasonCancellation', TextareaType::class, ['label' => 'Reason for Cancellation', 'required' => false])
            ->add('status', ChoiceType::class, ['choices' => Valuation::getStatuses(), 'required' => false])
            ->add('source', ChoiceType::class, ['choices' => Valuation::getSources(), 'required' => false])
            ->add('name', null, ['disabled' => true, 'required' => false])
            ->add('mobileNumber', null, ['label' => 'Mobile', 'disabled' => true, 'required' => false])
            ->add('emailAddress', EmailType::class, ['disabled' => true, 'required' => false])
            ->add('vehicleModel.make', null, ['disabled' => true, 'required' => false])
            ->add('vehicleModel.name', null, ['disabled' => true, 'required' => false])
            ->add('modelTypeName', TextType::class, ['disabled' => true, 'required' => false, 'label' => 'Vehicle Model Type'])
            ->add('vehicleYear', null, ['disabled' => true, 'required' => false])
            ->add('vehicleMileage', null, ['label' => 'Mileage (Kms)', 'disabled' => true, 'required' => false])
            ->add('vehicleBodyCondition', 'choice', ['choices' => ConditionType::getConditions(), 'disabled' => true, 'required' => false])
            ->add('vehicleColor', 'choice', ['choices' => ColorType::getColors(), 'disabled' => true, 'required' => false])
            ->add('vehicleOption', 'choice', ['choices' => OptionType::getOptions(), 'disabled' => true, 'required' => false])
            ->add('priceOnline', 'money', ['currency' => 'AED', 'label' => 'Price Online', 'disabled' => true, 'required' => false]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show->add('id')
            ->add('notes')
            ->add('reasonCancellation', TextareaType::class, ['label' => 'Reason for Cancellation', 'required' => false])
            ->add('status', 'choice', ['choices' => Valuation::getStatuses()])
            ->add('source', 'choice', ['choices' => Valuation::getSources()])
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
            ->add('vehicleOption', 'choice', ['choices' => OptionType::getOptions()])
            ->add('priceOnline', 'currency', ['currency' => 'AED'])
            ->add('createdAt', null, ['label' => 'Created']);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create')
            ->remove('delete')
            ->add('generateAppointment', $this->getRouterIdParameter().'/generateAppointment');
    }
}
