<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Form\DayType;
use Wbc\UtilityBundle\AdminDateRange;
use Wbc\VehicleBundle\Entity\Make;
use Wbc\VehicleBundle\Form as WbcVehicleType;

/**
 * Class AppointmentAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class AppointmentAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_order' => 'DESC',
        '_sort_by'    => 'createdAt',
    ];

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
    public function getTemplate($name)
    {
        switch ($name) {
            case 'create':
            case 'edit':
                return 'WbcBranchBundle:Admin:edit.html.twig';
            case 'show':
                return 'Admin/show.html.twig';
            case 'base_list_field':
                return 'WbcBranchBundle:Admin:base_list_field.html.twig';
            default:
                return parent::getTemplate($name);
        }
    }

    public function getExportFields()
    {
        return [
            'name',
            'mobileNumber',
            'emailAddress',
            'vehicleMake',
            'vehicleModel',
            'vehicleYear',
            'valuation.priceOnline',
            'dateBooked',
            'bookedAtTiming',
            'status',
            'source',
            'createdAt',
            'createdBy',
        ];
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
    protected function configureFormFields(FormMapper $formMapper): void
    {
        /** @var \Wbc\BranchBundle\Entity\Appointment $subject */
        $subject = $this->getSubject();
        $request = $this->getRequest();

        $formMapper->tab('Vehicle Information')
            ->with('Vehicle Details')
            ->add('vehicleYear', 'Wbc\VehicleBundle\Form\ModelYearType')
            ->add('vehicleMake', 'Wbc\VehicleBundle\Form\MakeType')
            ->add('vehicleModel', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
                'placeholder'   => '',
                'class'         => 'Wbc\VehicleBundle\Entity\Model',
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
                },
            ])
            ->add('vehicleModelType', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
                'required'      => false,
                'placeholder'   => '',
                'class'         => 'Wbc\VehicleBundle\Entity\ModelType',
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
                },
            ])
        ;

        if (null !== $subject) {
            $vehicleMake  = $subject->getVehicleMake();
            $vehicleModel = $subject->getVehicleModel();

            if ($vehicleMake || $request->isMethod('POST')) {
                $formMapper->add('vehicleModel', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
                    'placeholder'   => '',
                    'class'         => 'Wbc\VehicleBundle\Entity\Model',
                    'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject, $vehicleMake) {
                        return $entityRepository->createQueryBuilder('m')
                            ->where('m.make = :make')
                            ->andWhere('m.active = :active')
                            ->setParameter('make', $vehicleMake)
                            ->setParameter('active', true)
                            ->orderBy('m.name', 'ASC')
                            ;
                    },
                ]);
            }

            if ($vehicleModel || $request->isMethod('POST')) {
                $formMapper->add('vehicleModelType', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
                    'placeholder'   => '',
                    'class'         => 'Wbc\VehicleBundle\Entity\ModelType',
                    'label'         => 'Vehicle Trim',
                    'required'      => false,
                    'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject, $vehicleModel) {
                        return $entityRepository->createQueryBuilder('m')
                            ->where('m.model = :model')
                            ->setParameter('model', $vehicleModel)
                            ;
                    },
                ]);
            }
        }

        $formMapper->add('vehicleTransmission', 'Wbc\VehicleBundle\Form\TransmissionType', ['required' => false])
            ->add('vehicleMileage', 'Wbc\VehicleBundle\Form\MileageType')
            ->add('vehicleSpecifications', 'Wbc\VehicleBundle\Form\SpecificationType', ['required' => false])
            ->add('vehicleBodyCondition', 'Wbc\VehicleBundle\Form\ConditionType')
            ->add('vehicleColor', 'Wbc\VehicleBundle\Form\ColorType', ['required' => false])
            ->add('vehicleOption', 'Wbc\VehicleBundle\Form\OptionType', ['required' => false])
            ->end()
            ->end()
        ;

        $formMapper->tab('Customer Information')
            ->with('Customer Details')
            ->add('name')
            ->add('mobileNumber')
            ->add('emailAddress', 'Symfony\Component\Form\Extension\Core\Type\EmailType', ['required' => false])
            ->end()
            ->end()
            ->tab('Appointment Information')
            ->with('Timings')
            ->add('branch', 'Wbc\BranchBundle\Form\BranchType')
            ->add('bookedAt', 'Sonata\Form\Type\DateTimePickerType', [
                'dp_use_current' => false,
                'format'         => 'EE dd-MMM-yyyy H:mm',
                'dp_side_by_side'       => true,
            ])
        ;

        if ($subject->getValuation()) {
            $formMapper->end()
                ->with('Other Details')
                ->add('valuation.priceOnline', null, [
                    'label'     => 'Price Online (AED)',
                    'read_only' => true,
                    'disabled'  => true,
                    'required'  => false,
                ])
                ->add('status', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                    'choices'    => Appointment::getStatuses(),
                    'empty_data' => Appointment::STATUS_NEW,
                ])
                ->add('notes', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', ['required' => false])
                ->add('createdBy', null, ['read_only' => true, 'disabled' => true, 'required' => false])
                ->add('source', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', ['choices' => $this->getValuationSources(), 'required' => false])
            ;
        }

        $formMapper->add('smsSent', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', ['required' => false, 'disabled' => true])
            ->end()
            ->end()
        ;
    }

    /**
     * configureDatagridFilters.
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $now = new \DateTime();

        $datagridMapper->add('name')
            ->add('mobileNumber')
            ->add('vehicleMake', 'doctrine_orm_callback', [
                'callback' => function ($queryBuilder, $alias, $field, $value) {
                    if (!$value || ($value && !$value['value'] instanceof Make)) {
                        return false;
                    }

                    $queryBuilder->innerJoin($alias.'.vehicleModel', 'vehicleModel')
                        ->andWhere('vehicleModel.make = :make')
                        ->setParameter(':make', $value['value'])
                    ;

                    return true;
                },
                'field_type'    => 'entity',
                'field_options' => [
                    'class' => 'Wbc\VehicleBundle\Entity\Make',
                ],
            ])
            ->add('vehicleModel')
            ->add('vehicleYear')
            ->add('dateRange', 'doctrine_orm_callback', [
                'label'    => 'Booked Today/Tomorrow',
                'callback' => function ($queryBuilder, $alias, $field, $value) use ($now) {
                    $bookedAt = null;

                    if (!$value['value']) {
                        return;
                    }

                    if ('today' === $value['value']) {
                        $bookedAt = (new \DateTime())->format('Y-m-d');
                    } elseif ('tomorrow' === $value['value']) {
                        $bookedAt = (new \DateTime('+1 day'))->format('Y-m-d');
                    }

                    if (null !== $bookedAt) {
                        $queryBuilder->andWhere($alias.'.bookedAt BETWEEN :bookedAtFrom AND :bookedAtTo')
                            ->setParameter(':bookedAtFrom', $bookedAt.' 00:00:00')
                            ->setParameter(':bookedAtTo', $bookedAt.' 23:59:59')
                        ;
                    }

                    return true;
                },
                'field_type'    => 'choice',
                'field_options' => [
                    'choices' => [
                        'today'    => 'Today',
                        'tomorrow' => 'Tomorrow',
                    ],
                ],
            ])
            ->add('bookedAt', 'doctrine_orm_date_range', AdminDateRange::getDoctrineOrmDateRange('Date Range'))
            ->add('createdBy', null, [], 'entity', [
                'class'         => 'Wbc\UserBundle\Entity\User',
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('u')
                        ->where('u.enabled = true')
                        ;
                },
            ])
            ->add('status', 'doctrine_orm_choice', [
                'field_options' => ['choices' => Appointment::getStatuses()],
                'field_type'    => 'choice',
            ])
        ;
    }

    /**
     * configureListFields.
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $filterParams = $this->getFilterParameters();

        $listMapper->addIdentifier('id')
            ->add('name')
            ->add('mobileNumber', null, ['label' => 'Mobile'])
            ->add('details.vehicleMakeName', null, ['label' => 'Make'])
            ->add('details.vehicleModelName', null, ['label' => 'Model'])
            ->add('vehicleYear', null, ['label' => 'Year'])
            ->add('status', 'choice', ['choices' => Appointment::getStatuses(), 'editable' => true])
            ->add('source', 'choice', ['choices' => $this->getValuationSources(), 'editable' => true])
            ->add('vehicleOption', 'choice', ['choices' => WbcVehicleType\OptionType::getOptions()])
            ->add('valuation.priceOnline', 'currency', ['currency' => 'AED', 'label' => 'Online Valuation'])
            ->add('branch')
            ->add('bookedAt', 'date', ['label' => 'Date Booked'])
        ;

        if (isset($filterParams['dateRange']['value']) && 'today' === $filterParams['dateRange']['value']) {
            $listMapper->add('bookedAtTiming', 'text', [
                'label'                            => 'Timing',
                'sortable'                         => true,
                'associated_property'              => 'bookedAt',
                'template'                         => 'WbcBranchBundle:Admin/CRUD:list__field_timing.html.twig',
            ]);
        } else {
            $listMapper->add('branchTiming.adminListTiming', 'text', [
                'label'    => 'Timing',
                'template' => 'WbcBranchBundle:Admin/CRUD:list__field_timing.html.twig',
            ]);
        }

        $listMapper->add('createdAt', null, ['label' => 'Created'])
            ->add('createdBy')
            ->add('smsSent')
            ->add('_action', 'actions', [
                'actions' => [
                    'show'       => [],
                    'edit'       => [],
                    'delete'     => [],
                    'inspection' => ['template' => 'WbcBranchBundle:Admin/CRUD:list__action_inspection.html.twig'],
                    'sms'        => ['template' => 'WbcBranchBundle:Admin/CRUD:list__action_sms.html.twig'], ], ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper->tab('Vehicle Information')
            ->with('')
            ->add('vehicleYear')
            ->add('vehicleMake')
            ->add('vehicleModel')
            ->add('vehicleModelType', null, ['label' => 'Vehicle Trim'])
            ->add('vehicleTransmission', 'choice', ['choices' => WbcVehicleType\TransmissionType::getTransmissions()])
            ->add('vehicleMileage', 'choice', ['choices' => WbcVehicleType\MileageType::getMileages()])
            ->add('vehicleSpecifications', 'choice', ['choices' => WbcVehicleType\SpecificationType::getSpecifications()])
            ->add('vehicleBodyCondition', 'choice', ['choices' => WbcVehicleType\ConditionType::getConditions()])
            ->add('vehicleColor', 'choice', ['choices' => WbcVehicleType\ColorType::getColors()])
            ->add('vehicleOption', 'choice', ['choices' => WbcVehicleType\OptionType::getOptions()])
            ->end()
            ->end()
        ;

        $showMapper->tab('Customer Information')
            ->with('')
            ->add('name')
            ->add('mobileNumber')
            ->add('emailAddress')
            ->end()
            ->end()
        ;

        $showMapper->tab('Appointment Information')
            ->with('')
            ->add('branch')
            ->add('dayBooked', 'choice', ['label' => 'Days Booked'])
            ->add('bookedAt', 'date', ['label' => 'Date Booked'])
            ->add('bookedAtTiming')
            ->add('valuation.priceOnline', 'currency', ['currency' => 'AED'])
            ->add('status', 'choice', ['choices' => Appointment::getStatuses(), 'empty_data' => Appointment::STATUS_NEW])
            ->add('notes')
            ->add('source', 'choice', ['choices' => $this->getValuationSources()])
            ->add('createdBy')
            ->add('smsSent')
            ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->add('listVehicleModelsByMake', sprintf('modelsByMake/%s', $this->getRouterIdParameter()))
            ->add('listVehicleModelTypesByModel', sprintf('modelTypesByModel/%s', $this->getRouterIdParameter()))
            ->add('listBranchTimings', 'branchTimings/{branchId}/{date}')
            ->add('generateInspection', $this->getRouterIdParameter().'/generateInspection')
            ->add('sendSms', $this->getRouterIdParameter().'/sendSms')
        ;
    }

    private function getValuationSources()
    {
        return $this->getConfigurationPool()->getContainer()->get('wbc.static.parameter_manager')->getValuationSources();
    }
}
