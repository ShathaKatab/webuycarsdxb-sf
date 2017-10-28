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
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Entity\Branch;
use Wbc\BranchBundle\Entity\Inspection;
use Wbc\BranchBundle\Entity\Timing;
use Wbc\BranchBundle\Form\BranchType;
use Wbc\BranchBundle\Form\DayType;
use Wbc\VehicleBundle\Entity\Make;
use Wbc\VehicleBundle\Entity\Model;
use Wbc\VehicleBundle\Entity\ModelType;
use Wbc\VehicleBundle\Form as WbcVehicleType;

/**
 * Class DealAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class DealAdmin extends AbstractAdmin
{
    protected $datagridValues = ['_page' => 1, '_per_page' => 25, '_sort_order' => 'DESC', '_sort_by' => 'createdAt'];

    /**
     * {@inheritdoc}
     */
    public function getTemplate($name)
    {
        switch ($name) {
            case 'create':
            case 'edit':
                return 'WbcBranchBundle:Admin:edit.html.twig';
            default:
                return parent::getTemplate($name);
        }
    }

    public function getExportFields()
    {
        return ['id', 'name', 'mobileNumber', 'inspection.vehicleMake', 'inspection.vehicleModel', 'inspection.vehicleYear', 'inspection.priceOnline', 'inspection.priceOffered', 'inspection.priceExpected', 'createdAt', 'createdBy'];
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
        /** @var $subject \Wbc\BranchBundle\Entity\Deal */
        $subject = $this->getSubject();
        $request = $this->getRequest();

        $formMapper
            ->tab('Inspection Information')
            ->with('Deal Details')
            ->add('name')
            ->add('mobileNumber')
            ->add('emailAddress')
            ->add('pricePurchased', MoneyType::class, ['label' => 'Purchased Price', 'currency' => 'AED'])
            ->end()
            ->with('Inspection Details')
            ->add('inspection.priceOnline', MoneyType::class, [
                'label' => 'Online Price',
                'currency' => 'AED',
                'read_only' => true,
                'disabled' => true,
                'required' => false,
            ])
            ->add('inspection.priceOffered', MoneyType::class, [
                'label' => 'Offered Price',
                'currency' => 'AED',
                'required' => false,
                'disabled' => true,
                'read_only' => true,
            ])
            ->add('inspection.priceExpected', MoneyType::class, [
                'label' => 'Expected Price',
                'currency' => 'AED',
                'required' => false,
                'disabled' => true,
                'read_only' => true,
            ])
            ->add('inspection.status', ChoiceType::class, [
                'choices' => Inspection::getStatuses(),
                'empty_data' => Inspection::STATUS_NEW,
                'required' => false,
                'disabled' => true,
                'read_only' => true,
            ])
            ->end()
            ->with('Vehicle Details')
            ->add('inspection.vehicleYear', WbcVehicleType\ModelYearType::class, [
                'required' => false,
                'disabled' => true,
                'read_only' => true,
            ])
            ->add('inspection.vehicleMake', WbcVehicleType\MakeType::class, ['required' => false, 'disabled' => true, 'read_only' => true])
            ->add('inspection.vehicleModel', EntityType::class, [
                'placeholder' => '', 'class' => Model::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
                },
                'required' => false,
                'disabled' => true,
                'read_only' => true,
            ])
            ->add('inspection.vehicleModelType', EntityType::class, [
                'required' => false,
                'placeholder' => '',
                'class' => ModelType::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
                },
                'disabled' => true,
                'read_only' => true,
            ]);
        if ($subject) {
            $inspection = $subject->getInspection();
            $vehicleMake = $inspection->getVehicleMake();
            $vehicleModel = $inspection->getVehicleModel();

            if ($vehicleMake || $request->isMethod('POST')) {
                $formMapper->add('inspection.vehicleModel', EntityType::class, [
                    'placeholder' => '',
                    'class' => Model::class,
                    'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject, $vehicleMake) {
                        return $entityRepository->createQueryBuilder('m')->where('m.make = :make')->andWhere('m.active = :active')->setParameter('make', $vehicleMake)->setParameter('active', true)->orderBy('m.name', 'ASC');
                    },
                    'required' => false,
                    'disabled' => true,
                    'read_only' => true,
                ]);
            }

            if ($vehicleModel || $request->isMethod('POST')) {
                $formMapper->add('inspection.vehicleModelType', EntityType::class, [
                    'placeholder' => '',
                    'class' => ModelType::class,
                    'label' => 'Vehicle Trim',
                    'required' => false,
                    'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject, $vehicleModel) {
                        return $entityRepository->createQueryBuilder('m')->where('m.model = :model')->setParameter('model', $vehicleModel);
                    },
                    'disabled' => true,
                    'read_only' => true,
                ]);
            }
        }

        $formMapper->add('inspection.vehicleTransmission', WbcVehicleType\TransmissionType::class, [
            'required' => false,
            'disabled' => true,
            'read_only' => true,
        ])
            ->add('inspection.vehicleMileage', WbcVehicleType\MileageType::class, [
                'required' => false,
                'disabled' => true,
                'read_only' => true,
            ])
            ->add('inspection.vehicleSpecifications', WbcVehicleType\SpecificationType::class, [
                'required' => false,
                'disabled' => true,
                'read_only' => true,
            ])
            ->add('inspection.vehicleBodyCondition', WbcVehicleType\ConditionType::class, [
                'required' => false,
                'disabled' => true,
                'read_only' => true,
            ])
            ->add('inspection.vehicleColor', WbcVehicleType\ColorType::class, [
                'required' => false,
                'disabled' => true,
                'read_only' => true,
            ])
            ->end()
            ->end();

        $formMapper->tab('Appointment Information')
            ->with('Customer Details')->add('appointment.name', TextType::class, ['read_only' => true, 'required' => false])
            ->add('appointmentMobileNumber', TextType::class, ['read_only' => true, 'required' => false])
            ->add('appointmentEmailAddress', TextType::class, ['read_only' => true, 'required' => false])
            ->end()
            ->with('Timings')
            ->add('appointmentBranch', BranchType::class, ['disabled' => true, 'read_only' => true, 'required' => false])
            ->add('appointmentDateBooked', 'sonata_type_date_picker', [
                'dp_min_date' => new \DateTime(),
                'dp_max_date' => '+30',
                'dp_use_current' => false,
                'format' => 'EE dd-MMM-yyyy',
                'disabled' => true,
                'read_only' => true,
                'required' => false,
            ])
            ->add('appointmentBranchTiming', ChoiceType::class, ['read_only' => true, 'required' => false])
            ->add('appointmentNotes', TextareaType::class, ['disabled' => true, 'read_only' => true, 'required' => false]);

        if (($subject && $subject->getAppointment()->getBranch() && $subject->getAppointment()->getDateBooked()) || $request->isMethod('POST')) {
            $appointment = $subject->getAppointment();
            $formMapper->add('appointmentBranchTiming', EntityType::class, [
                'class' => Timing::class,
                'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject, $appointment) {
                    return $entityRepository->createQueryBuilder('t')
                        ->select('t')
                        ->innerJoin('t.branch', 'branch', 'WITH', 'branch = :branch')
                        ->where('t.dayBooked = :dayBooked')
                        ->setParameter('branch', $appointment->getBranch())
                        ->setParameter('dayBooked', $appointment->getDateBooked()->format('N'))->orderBy('t.dayBooked', 'ASC')
                        ->addOrderBy('t.from', 'ASC');
                },
                'disabled' => true,
                'read_only' => true,
                'required' => false,
            ]);
        }
        $formMapper->end()->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $now = new \DateTime();
        $datagridMapper->add('name', null, ['label' => 'Name'])
            ->add('mobileNumber', null, ['label' => 'Mobile Number'])
            ->add('inspection.vehicleMake', 'doctrine_orm_callback', [
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
                'field_options' => ['class' => Make::class],
                'label' => 'Vehicle Make',
            ])
            ->add('inspection.vehicleModel')
            ->add('inspection.vehicleYear')
            ->add('appointment.dateRange', 'doctrine_orm_callback', [
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
                    ], ], ])
            ->add('appointment.dateBooked', 'doctrine_orm_date_range', [
                'label' => 'Date Range',
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
        $listMapper->addIdentifier('id')
            ->add('name')
            ->add('mobileNumber', null, ['label' => 'Mobile'])
            ->add('inspection.vehicleMake', null, ['label' => 'Make'])
            ->add('inspection.vehicleModel', null, ['label' => 'Model'])
            ->add('inspection.vehicleYear', null, ['label' => 'Year'])
            ->add('inspection.priceOnline', 'currency', ['currency' => 'AED', 'label' => 'Online Valuation'])
            ->add('inspection.priceOffered', 'currency', ['currency' => 'AED', 'label' => 'Offered Price'])
            ->add('inspection.priceExpected', 'currency', ['currency' => 'AED', 'label' => 'Expected Price'])
            ->add('pricePurchased', 'currency', ['currency' => 'AED', 'label' => 'Purchased Price'])
            ->add('dateBookedString', null, ['label' => 'Date Booked'])
            ->add('timingString', null, ['label' => 'Timing'])
            ->add('createdAt', null, ['label' => 'Created'])
            ->add('createdBy', null, ['placeholder' => 'User'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->tab('Deal Information')
            ->with('Deal Details')
            ->add('name')
            ->add('mobileNumber')
            ->add('emailAddress')
            ->end()
            ->with('Inspection Details')
            ->add('inspection.priceOnline', null, ['label' => 'Online Price (AED)'])
            ->add('inspection.priceOffered', null, ['label' => 'Offered Price (AED)'])
            ->add('inspection.priceExpected', null, ['label' => 'Expected Price (AED)'])
            ->add('inspection.status', ChoiceType::class, ['choices' => Inspection::getStatuses()])
            ->end()
            ->with('Vehicle Details')
            ->add('inspection.vehicleYear')
            ->add('inspection.vehicleMake')
            ->add('inspection.vehicleModel')
            ->add('inspection.vehicleModelType', null, ['label' => 'Vehicle Trim'])
            ->add('inspection.vehicleTransmission', 'choice', ['choices' => WbcVehicleType\TransmissionType::getTransmissions()])
            ->add('inspection.vehicleMileage', 'choice', ['choices' => WbcVehicleType\MileageType::getMileages()])
            ->add('inspection.vehicleSpecifications', 'choice', ['choices' => WbcVehicleType\SpecificationType::getSpecifications()])
            ->add('inspection.vehicleBodyCondition', 'choice', ['choices' => WbcVehicleType\ConditionType::getConditions()])
            ->end()
            ->end();
        $showMapper->tab('Appointment Information')
            ->with('Customer Details')
            ->add('appointmentName')
            ->add('appointmentMobileNumber')
            ->add('appointmentEmailAddress')
            ->end()
            ->with('Timings')
            ->add('appointmentBranch')
            ->add('appointmentDayBooked', 'choice', ['choices' => DayType::getDays(), 'label' => 'Day Booked'])
            ->add('dateBookedString')
            ->add('appointmentBranchTiming')
            ->add('appointmentNotes')
            ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create')->remove('delete')
            ->add('listVehicleModelsByMake', sprintf('modelsByMake/%s', $this->getRouterIdParameter()))
            ->add('listVehicleModelTypesByModel', sprintf('modelTypesByModel/%s', $this->getRouterIdParameter()))
            ->add('listBranchTimings', 'branchTimings/{branchId}/{day}');
    }
}
