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
use Wbc\BranchBundle\Entity\Inspection;
use Wbc\UtilityBundle\AdminDateRange;
use Wbc\VehicleBundle\Entity\Make;
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
    public function getExportFields(): array
    {
        return [
            'id',
            'appointment.name',
            'appointment.emailAddress',
            'appointment.mobileNumber',
            'vehicleMake',
            'vehicleModel',
            'vehicleYear',
            'appointment.bookedAt',
            'appointment.bookedAtTiming',
            'source',
            'status',
            'priceOnline',
            'priceOffered',
            'priceExpected',
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

    public function validate(ErrorElement $errorElement, $object)
    {
        parent::validate($errorElement, $object);

        if ($this->getConfigurationPool()->getContainer()->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            return;
        }

        $em             = $this->getModelManager()->getEntityManager($this->getClass());
        $originalObject = $em->getUnitOfWork()->getOriginalEntityData($object);
        $newStatus      = $object->getStatus();
        $oldStatus      = $originalObject['status'];
        $canTransition  = false;

        if ($newStatus === $oldStatus) {
            return; //status not changed
        }

        if ('invalid' === $newStatus && 'new' === $oldStatus) {
            $canTransition = true;
        }

        if ('offer_accepted' === $newStatus && \in_array($oldStatus, ['new', 'pending'], true)) {
            $canTransition = true;
        }

        if ('offer_rejected' === $newStatus && \in_array($oldStatus, ['new', 'pending'], true)) {
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
        /** @var \Wbc\BranchBundle\Entity\Inspection $subject */
        $subject = $this->getSubject();
        $request = $this->getRequest();

        $formMapper->tab('Inspection Information')
            ->with('Inspection Details')
            ->add('priceOnline', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', [
                'label'     => 'Online Price',
                'currency'  => 'AED',
                'read_only' => true,
                'disabled'  => true,
                'required'  => false,
            ])
            ->add('priceOffered', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', [
                'label'    => 'Offered Price',
                'currency' => 'AED',
                'required' => false,
            ])
            ->add('priceExpected', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', [
                'label'    => 'Expected Price',
                'currency' => 'AED',
                'required' => false,
            ])
            ->add('status', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', [
                'choices'    => Inspection::getStatuses(),
                'empty_data' => Inspection::STATUS_NEW,
            ])
            ->add('notes', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', [
                'label'    => 'Inspection Notes',
                'required' => false,
            ])
            ->add('source', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', ['choices' => $this->getValuationSources(), 'required' => false])
            ->end()
            ->with('Vehicle Details')
            ->add('vehicleYear', 'Wbc\VehicleBundle\Form\ModelYearType')
            ->add('vehicleMake', 'Wbc\VehicleBundle\Form\MakeType')
            ->add('vehicleModel', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
                'placeholder'   => '',
                'class'         => 'Wbc\VehicleBundle\Entity\Model',
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
                }, ])
            ->add('vehicleModelType', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', [
                'required'      => false,
                'placeholder'   => '',
                'class'         => 'Wbc\VehicleBundle\Entity\ModelType',
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
                },
            ])
        ;

        if ($subject) {
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
            ->end()
            ->end()
        ;

        $formMapper
            ->tab('Appointment Information')
            ->with('Customer Details')
            ->add('appointment.name', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'read_only' => true,
                'required'  => false,
            ])
            ->add('appointment.mobileNumber', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'read_only' => true,
                'required'  => false,
            ])
            ->add('appointment.emailAddress', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'read_only' => true,
                'required'  => false,
            ])
            ->end()
            ->with('Timings')
            ->add('appointment.branch', 'Wbc\BranchBundle\Form\BranchType', [
                'disabled'  => true,
                'read_only' => true,
                'required'  => false,
            ])
            ->add('dayBooked', 'Symfony\Component\Form\Extension\Core\Type\TextType', ['label' => 'Day Booked', 'read_only' => true])
            ->add('appointment.bookedAt', 'Sonata\Form\Type\DatePickerType', ['label' => 'Date Booked', 'read_only' => true])
            ->add('bookedAtTiming', 'Symfony\Component\Form\Extension\Core\Type\TextType', ['label' => 'Timing', 'read_only' => true])
            ->add('appointment.notes', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', ['disabled' => true, 'read_only' => true, 'required' => false])
        ;
        $formMapper->end()
            ->end()
        ;
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
                'field_type'    => 'entity',
                'field_options' => ['class' => 'Wbc\VehicleBundle\Entity\Make'],
                'label'         => 'Vehicle Make',
            ])
            ->add('vehicleModel')
            ->add('vehicleYear')
            ->add('appointment.dateRange', 'doctrine_orm_callback', [
                'label'    => 'Booked Today/Tomorrow',
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
                'field_type'    => 'choice',
                'field_options' => [
                    'choices' => [
                        'today'    => 'Today',
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
                'label'    => 'Online Valuation',
            ])
            ->add('priceOffered', 'currency', [
                'currency' => 'AED',
                'label'    => 'Offered Price',
            ])
            ->add('priceExpected', 'currency', [
                'currency' => 'AED',
                'label'    => 'Expected Price',
            ])
            ->add('dayBooked', null, ['label' => 'Day Booked'])
            ->add('appointment.bookedAt', 'date', ['label' => 'Date Booked'])
            ->add('bookedAtTiming', null, ['label' => 'Timing'])
            ->add('createdAt', null, ['label' => 'Created'])
            ->add('createdBy', null, ['placeholder' => 'User'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show'      => [],
                    'edit'      => [],
                    'delete'    => [],
                    'inventory' => ['template' => 'WbcBranchBundle:Admin/CRUD:list__action_inventory.html.twig'],
                ],
            ])
        ;
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
            ->end()
        ;
        $showMapper->tab('Appointment Information')
            ->with('Customer Details')
            ->add('appointment.name')
            ->add('appointment.mobileNumber')
            ->add('appointment.emailAddress')
            ->end()
            ->with('Timings')
            ->add('appointment.branch', null, ['label' => 'Branch'])
            ->add('dayBooked', null, ['label' => 'Days Booked'])
            ->add('appointment.bookedAt', 'date', ['label' => 'Date Booked'])
            ->add('bookedAtTiming', null, ['label' => 'Timing'])
            ->add('appointment.notes', 'textarea', ['label' => 'Notes'])

            ->end()
            ->end()
        ;
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
