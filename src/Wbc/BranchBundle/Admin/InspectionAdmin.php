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
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Entity\Inspection;
use Wbc\BranchBundle\Entity\Timing;
use Wbc\BranchBundle\Form\BranchType;
use Wbc\BranchBundle\Form\DayType;
use Wbc\UtilityBundle\AdminDateRange;
use Wbc\VehicleBundle\Entity\Make;
use Wbc\VehicleBundle\Entity\Model;
use Wbc\VehicleBundle\Entity\ModelType;
use Wbc\VehicleBundle\Form as WbcVehicleType;

/**
 * Class InspectionAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class InspectionAdmin extends AbstractAdmin
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

    /**
     * {@inheritdoc}
     */
    public function getExportFormats()
    {
        return ['xls'];
    }

    /**
     * @return array
     */
    public function getExportFields()
    {
        return ['id', 'appointment.name', 'appointment.emailAddress', 'appointment.mobileNumber', 'vehicleMake', 'vehicleModel', 'vehicleYear', 'appointment.dateBooked', 'appointment.branchTiming', 'source', 'status', 'priceOnline', 'priceOffered', 'priceExpected', 'createdAt', 'createdBy'];
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

    public function validate(ErrorElement $errorElement, $object)
    {
        parent::validate($errorElement, $object);

        if($this->getConfigurationPool()->getContainer()->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
            return;
        }

        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $originalObject = $em->getUnitOfWork()->getOriginalEntityData($object);
        $newStatus = $object->getStatus();
        $oldStatus = $originalObject['status'];
        $canTransition = false;

        if ($newStatus === $oldStatus) {
            return; //status not changed
        }

        if ('invalid' === $newStatus && $oldStatus === 'new') {
            $canTransition = true;
        }

        if ('offer_accepted' === $newStatus && in_array($oldStatus, ['new', 'pending'])) {
            $canTransition = true;
        }

        if ('offer_rejected' === $newStatus && in_array($oldStatus, ['new', 'pending'])) {
            $canTransition = true;
        }

        if ('pending' === $newStatus && 'new' === $oldStatus) {
            $canTransition = true;
        }

        if (!$canTransition) {
            $errorElement->with('status')->addViolation('You cannot change the status to this value')->end();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var $subject \Wbc\BranchBundle\Entity\Inspection */
        $subject = $this->getSubject();
        $request = $this->getRequest();

        $formMapper->tab('Inspection Information')
            ->with('Inspection Details')
            ->add('priceOnline', MoneyType::class, [
                'label' => 'Online Price',
                'currency' => 'AED',
                'read_only' => true,
                'disabled' => true,
                'required' => false,
            ])
            ->add('priceOffered', MoneyType::class, [
                'label' => 'Offered Price',
                'currency' => 'AED',
                'required' => false,
            ])
            ->add('priceExpected', MoneyType::class, [
                'label' => 'Expected Price',
                'currency' => 'AED',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => Inspection::getStatuses(),
                'empty_data' => Inspection::STATUS_NEW,
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Inspection Notes',
                'required' => false,
            ])
            ->add('source', ChoiceType::class, ['choices' => $this->getValuationSources(), 'required' => false])
            ->end()
            ->with('Vehicle Details')
            ->add('vehicleYear', WbcVehicleType\ModelYearType::class)
            ->add('vehicleMake', WbcVehicleType\MakeType::class)
            ->add('vehicleModel', EntityType::class, [
                'placeholder' => '',
                'class' => Model::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
                }, ])->add('vehicleModelType', EntityType::class, [
                'required' => false,
                'placeholder' => '',
                'class' => ModelType::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
                },
            ]);

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
            ->add('vehicleColor', WbcVehicleType\ColorType::class, ['required' => false])
            ->end()
            ->end();

        $formMapper
            ->tab('Appointment Information')
            ->with('Customer Details')
            ->add('appointment.name', TextType::class, [
                'read_only' => true,
                'required' => false,
            ])
            ->add('appointment.mobileNumber', TextType::class, [
                'read_only' => true,
                'required' => false,
            ])
            ->add('appointment.emailAddress', TextType::class, [
                'read_only' => true,
                'required' => false,
            ])
            ->end()
            ->with('Timings')
            ->add('appointment.branch', BranchType::class, [
                'disabled' => true,
                'read_only' => true,
                'required' => false,
            ])
            ->add('appointment.dateBooked', DatePickerType::class, [
                'dp_min_date' => new \DateTime(),
                'dp_max_date' => '+30',
                'dp_use_current' => false,
                'format' => 'EE dd-MMM-yyyy',
                'disabled' => true,
                'read_only' => true,
                'required' => false,
            ])
            ->add('appointment.branchTiming', ChoiceType::class, [
                'read_only' => true,
                'required' => false,
            ])
            ->add('appointment.notes', TextareaType::class, ['disabled' => true, 'read_only' => true, 'required' => false]);

        if (($subject && $subject->getAppointment()->getBranch() && $subject->getAppointment()->getDateBooked()) || $request->isMethod('POST')) {
            $appointment = $subject->getAppointment();
            $formMapper->add('appointment.branchTiming', EntityType::class, [
                'class' => Timing::class,
                'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject, $appointment) {
                    return $entityRepository->createQueryBuilder('t')
                        ->select('t')
                        ->innerJoin('t.branch', 'branch', 'WITH', 'branch = :branch')
                        ->where('t.dayBooked = :dayBooked')
                        ->setParameter('branch', $appointment->getBranch())
                        ->setParameter('dayBooked', $appointment->getDateBooked()->format('N'))
                        ->orderBy('t.dayBooked', 'ASC')
                        ->addOrderBy('t.from', 'ASC');
                },
                'disabled' => true,
                'read_only' => true,
                'required' => false,
            ]);
        }
        $formMapper->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $now = new \DateTime();
        $datagridMapper->add('appointment.name', null, ['label' => 'Name'])
            ->add('appointment.mobileNumber', null, ['label' => 'Mobile Number'])
            ->add('vehicleMake', 'doctrine_orm_callback', [
                'callback' => function ($queryBuilder, $alias, $field, $value) {
                    if (!$value || ($value && !$value['value'] instanceof Make)) {
                        return false;
                    }
                    $queryBuilder->innerJoin($alias.'.vehicleModel', 'vehicleModel')->andWhere('vehicleModel.make = :make')->setParameter(':make', $value['value']);

                    return true;
                },
                'field_type' => 'entity',
                'field_options' => ['class' => Make::class],
                'label' => 'Vehicle Make',
            ])
            ->add('vehicleModel')
            ->add('vehicleYear')
            ->add('appointment.dateRange', 'doctrine_orm_callback', [
                'label' => 'Booked Today/Tomorrow',
                'callback' => function ($queryBuilder, $alias, $field, $value) use ($now) {
                    $dateBooked = null;
                    if (!$value['value']) {
                        return;
                    }
                    if ('today' === $value['value']) {
                        $dateBooked = (new \DateTime())->format('Y-m-d');
                    } elseif ('tomorrow' === $value['value']) {
                        $dateBooked = (new \DateTime('+1 day'))->format('Y-m-d');
                    }
                    if ($dateBooked) {
                        $queryBuilder->andWhere($alias.'.dateBooked = :dateBooked')->setParameter(':dateBooked', $dateBooked);
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
            ->add('appointment.dateBooked', 'doctrine_orm_date_range', AdminDateRange::getDoctrineOrmDateRange('Date Range'))
            ->add('status', 'doctrine_orm_choice', [
                'field_options' => [
                    'choices' => Inspection::getStatuses(), ],
                'field_type' => 'choice',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id')
            ->add('appointment.name', null, ['label' => 'Name'])
            ->add('appointment.mobileNumber', null, ['label' => 'Mobile'])
            ->add('vehicleMake', null, ['label' => 'Make'])
            ->add('vehicleModel', null, ['label' => 'Model'])
            ->add('vehicleYear', null, ['label' => 'Year'])
            ->add('status', 'choice', ['choices' => Inspection::getStatuses()])
            ->add('source', 'choice', ['choices' => $this->getValuationSources(), 'editable' => true])
            ->add('priceOnline', 'currency', [
                'currency' => 'AED',
                'label' => 'Online Valuation',
            ])
            ->add('priceOffered', 'currency', [
                'currency' => 'AED',
                'label' => 'Offered Price',
            ])
            ->add('priceExpected', 'currency', [
                'currency' => 'AED',
                'label' => 'Expected Price',
            ])
            ->add('appointment.dateBooked')
            ->add('branchTiming.timingString', null, ['label' => 'Timing'])
            ->add('createdAt', null, ['label' => 'Created'])
            ->add('createdBy', null, ['placeholder' => 'User'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'inventory' => ['template' => 'WbcBranchBundle:Admin/CRUD:list__action_inventory.html.twig'],
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->tab('Inspection Information')
            ->with('Inspection Details')
            ->add('priceOnline', null, ['label' => 'Online Price (AED)'])
            ->add('priceOffered', null, ['label' => 'Offered Price (AED)'])
            ->add('priceExpected', null, ['label' => 'Expected Price (AED)'])
            ->add('status', 'choice', ['choices' => Inspection::getStatuses()])
            ->add('source', 'choice', ['choices' => $this->getValuationSources()])
            ->add('notes', 'textarea')
            ->end()
            ->with('Vehicle Details')
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
        $showMapper->tab('Appointment Information')
            ->with('Customer Details')
            ->add('appointment.name')
            ->add('appointment.mobileNumber')
            ->add('appointment.emailAddress')
            ->end()
            ->with('Timings')
            ->add('appointment.branch')
            ->add('appointment.dayBooked', 'choice', ['choices' => DayType::getDays(), 'label' => 'Days Booked'])
            ->add('appointment.dateBooked')
            ->add('appointment.branchTiming')
            ->add('appointment.notes', 'textarea')

            ->end()
            ->end();
        $showMapper->tab('Appointment Information')->with('')->end()->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create')
            ->remove('delete')
            ->add('listVehicleModelsByMake', sprintf('modelsByMake/%s', $this->getRouterIdParameter()))
            ->add('listVehicleModelTypesByModel', sprintf('modelTypesByModel/%s', $this->getRouterIdParameter()))
            ->add('listBranchTimings', 'branchTimings/{branchId}/{date}')
            ->add('generateDeal', $this->getRouterIdParameter().'/generateDeal')
        ;
    }

    private function getValuationSources()
    {
        return $this->getConfigurationPool()->getContainer()->get('wbc.static.parameter_manager')->getValuationSources();
    }
}
