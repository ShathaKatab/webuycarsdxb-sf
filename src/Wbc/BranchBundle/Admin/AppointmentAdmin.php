<?php

namespace Wbc\BranchBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Entity\Timing;
use Wbc\BranchBundle\Form\BranchType;
use Wbc\BranchBundle\Form\DayType;
use Wbc\VehicleBundle\Entity\Make;
use Wbc\VehicleBundle\Entity\Model;
use Wbc\VehicleBundle\Entity\ModelType;
use Wbc\VehicleBundle\Form as WbcVehicleType;

/**
 * Class AppointmentAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class AppointmentAdmin extends AbstractAdmin
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
    public function getTemplate($name)
    {
        switch ($name) {
            case 'create':
            case 'edit':
                return 'WbcBranchBundle:Admin:edit.html.twig';
            case 'base_list_field':
                return 'WbcBranchBundle:Admin:base_list_field.html.twig';
            default:
                return parent::getTemplate($name);
        }
    }

    public function getExportFields()
    {
        return ['name', 'mobileNumber', 'vehicleMake', 'vehicleModel', 'vehicleYear', 'valuation.priceOnline', 'dateBooked', 'branchTiming', 'createdAt', 'createdBy'];
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
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var $subject \Wbc\BranchBundle\Entity\Appointment */
        $subject = $this->getSubject();
        $request = $this->getRequest();

        $formMapper->tab('Vehicle Information')
            ->with('Vehicle Details')
            ->add('vehicleYear', WbcVehicleType\ModelYearType::class)
            ->add('vehicleMake', WbcVehicleType\MakeType::class)
            ->add('vehicleModel', EntityType::class, [
                'placeholder' => '',
                'class' => Model::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
                },
            ])
            ->add('vehicleModelType', EntityType::class, [
                'required' => false,
                'placeholder' => '',
                'class' => ModelType::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
                },
            ])
        ;

        if ($subject) {
            $vehicleMake = $subject->getVehicleMake();
            $vehicleModel = $subject->getVehicleModel();

            if ($vehicleMake || $request->isMethod('POST')) {
                $formMapper->add('vehicleModel', EntityType::class, [
                    'placeholder' => '',
                    'class' => Model::class,
                    'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject, $vehicleMake) {
                        return $entityRepository->createQueryBuilder('m')
                            ->where('m.make = :make')
                            ->andWhere('m.active = :active')
                            ->setParameter('make', $vehicleMake)
                            ->setParameter('active', true)
                            ->orderBy('m.name', 'ASC');
                    },
                ]);
            }

            if ($vehicleModel || $request->isMethod('POST')) {
                $formMapper->add('vehicleModelType', EntityType::class, [
                    'placeholder' => '',
                    'class' => ModelType::class,
                    'label' => 'Vehicle Trim',
                    'required' => false,
                    'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject, $vehicleModel) {
                        return $entityRepository->createQueryBuilder('m')
                            ->where('m.model = :model')
                            ->setParameter('model', $vehicleModel);
                    },
                ]);
            }
        }

        $formMapper->add('vehicleTransmission', WbcVehicleType\TransmissionType::class, ['required' => false])
            ->add('vehicleMileage', WbcVehicleType\MileageType::class)
            ->add('vehicleSpecifications', WbcVehicleType\SpecificationType::class, ['required' => false])
            ->add('vehicleBodyCondition', WbcVehicleType\ConditionType::class)
            ->add('vehicleColor', WbcVehicleType\ColorType::class)
            ->end()
            ->end();

        $formMapper->tab('Customer Information')
            ->with('Customer Details')
            ->add('name')
            ->add('mobileNumber')
            ->add('emailAddress', EmailType::class, ['required' => false])
            ->end()
            ->end()
            ->tab('Appointment Information')
            ->with('Timings')
            ->add('branch', BranchType::class)
            ->add('dateBooked', 'sonata_type_date_picker', [
                'dp_min_date' => new \DateTime(),
                'dp_max_date' => '+30',
                'dp_use_current' => false,
                'format' => 'EE dd-MMM-yyyy',
            ])
            ->add('branchTiming', ChoiceType::class);

        if (($subject && $subject->getBranch() && $subject->getDateBooked()) || $request->isMethod('POST')) {
            $formMapper->add('branchTiming', EntityType::class, [
                'class' => Timing::class,
                'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject) {
                    return $entityRepository->createQueryBuilder('t')
                        ->select('t')
                        ->innerJoin('t.branch', 'branch', 'WITH', 'branch = :branch')
                        ->where('t.dayBooked = :dayBooked')
                        ->setParameter('branch', $subject->getBranch())
                        ->setParameter('dayBooked', $subject->getDateBooked()->format('N'))
                        ->orderBy('t.dayBooked', 'ASC')
                        ->addOrderBy('t.from', 'ASC');
                },
            ]);
        }

        if ($subject->getValuation()) {
            $formMapper->end()
                ->with('')
                ->add('valuation.priceOnline', null, [
                    'label' => 'Price Online (AED)',
                    'read_only' => true,
                    'disabled' => true,
                    'required' => false,
                ])
                ->add('status', ChoiceType::class, [
                    'choices' => Appointment::getStatuses(),
                    'empty_data' => Appointment::STATUS_NEW,
                ])
                ->add('notes', TextareaType::class, ['required' => false])
                ->end();
        }

        $formMapper->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
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
                        ->setParameter(':make', $value['value']);

                    return true;
                },
                'field_type' => 'entity',
                'field_options' => [
                    'class' => Make::class,
                ],
            ])
            ->add('vehicleModel')
            ->add('vehicleYear')
            ->add('dateRange', 'doctrine_orm_callback', [
                'label' => 'Booked Today/Tomorrow',
                'callback' => function ($queryBuilder, $alias, $field, $value) use ($now) {
                    $dateBooked = null;

                    if (!$value['value']) {
                        return;
                    }

                    if ($value['value'] === 'today') {
                        $dateBooked = (new \DateTime())->format('Y-m-d');
                    } elseif ($value['value'] === 'tomorrow') {
                        $dateBooked = (new \DateTime('+1 day'))->format('Y-m-d');
                    }

                    if ($dateBooked) {
                        $queryBuilder->andWhere($alias.'.dateBooked = :dateBooked')
                            ->setParameter(':dateBooked', $dateBooked);
                    }

                    return true;
                },
                'field_type' => 'choice',
                'field_options' => [
                    'choices' => [
                        'today' => 'Today',
                        'tomorrow' => 'Tomorrow',
                    ],
                ],
            ])
            ->add('dateBooked', 'doctrine_orm_date_range', [
                'label' => 'Date Range',
                'field_type' => 'sonata_type_date_range_picker',
                'start_options' => [
                    'years' => range($now->format('Y'), (int) ($now->format('Y')) + 1),
                    'dp_min_date' => (new \DateTime('-1 month'))->format('d/M/Y'),
                    'dp_max_date' => (new \DateTime('+1 month'))->format('d/M/Y'),
                    'dp_default_date' => $now->format('m/d/Y'),
                ],
                'end_options' => [
                    'years' => range($now->format('Y'), (int) ($now->format('Y')) + 1),
                    'dp_min_date' => (new \DateTime('-1 month'))->format('d/M/Y'),
                    'dp_max_date' => (new \DateTime('+1 month'))->format('d/M/Y'),
                    'dp_default_date' => $now->format('m/d/Y'),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id')
            ->add('name')
            ->add('mobileNumber', null, ['label' => 'Mobile'])
            ->add('details.vehicleMakeName', null, ['label' => 'Make'])
            ->add('details.vehicleModelName', null, ['label' => 'Model'])
            ->add('vehicleYear', null, ['label' => 'Year'])
            ->add('status', 'choice', ['choices' => Appointment::getStatuses(), 'editable' => true])
            ->add('valuation.priceOnline', 'currency', ['currency' => 'AED', 'label' => 'Online Valuation'])
            ->add('dateBooked')
            ->add('branchTiming.timingString', null, ['label' => 'Timing'])
            ->add('createdAt', null, ['label' => 'Created'])
            ->add('createdBy', null, ['placeholder' => 'User'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'inspection' => ['template' => 'WbcBranchBundle:Admin/CRUD:list__action_inspection.html.twig'],
                ], ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
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
            ->end()
            ->end();

        $showMapper->tab('Customer Information')
            ->with('')
            ->add('name')
            ->add('mobileNumber')
            ->add('emailAddress')
            ->end()
            ->end();

        $showMapper->tab('Appointment Information')
            ->with('')
            ->add('branch')
            ->add('branchTiming.dayBooked', 'choice', ['choices' => DayType::getDays(), 'label' => 'Day Booked'])
            ->add('dateBooked')
            ->add('branchTiming')
            ->add('valuation.priceOnline', 'currency', ['currency' => 'AED'])
            ->add('status', 'choice', ['choices' => Appointment::getStatuses(), 'empty_data' => Appointment::STATUS_NEW])
            ->add('notes')
            ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('listVehicleModelsByMake', sprintf('modelsByMake/%s', $this->getRouterIdParameter()))
            ->add('listVehicleModelTypesByModel', sprintf('modelTypesByModel/%s', $this->getRouterIdParameter()))
            ->add('listBranchTimings', 'branchTimings/{branchId}/{day}')
            ->add('generateInspection', $this->getRouterIdParameter().'/generateInspection');
    }
}
