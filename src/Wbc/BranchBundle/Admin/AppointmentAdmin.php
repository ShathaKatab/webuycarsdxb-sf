<?php

namespace Wbc\BranchBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Wbc\BranchBundle\Entity\Timing;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\BranchBundle\Form\BranchType;
use Wbc\BranchBundle\Form\DayType;
use Wbc\VehicleBundle\Entity\ModelType;
use Wbc\VehicleBundle\Form as WbcVehicleType;
use Wbc\VehicleBundle\Entity\Model;

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

            default:
                return parent::getTemplate($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var $subject \Wbc\BranchBundle\Entity\Appointment*/
        $subject = $this->getSubject();
        $request = $this->getRequest();

        $formMapper->tab('Vehicle Information')
            ->with('Vehicle Details')
            ->add('vehicleYear', WbcVehicleType\ModelYearType::class)
            ->add('vehicleMake', WbcVehicleType\MakeType::class)
            ->add('vehicleModel', ChoiceType::class)
            ->add('vehicleModelType', WbcVehicleType\ModelTypeSelectorType::class, [
                'label' => 'Vehicle Trim',
                'required' => false, ])
        ;

        if ($subject) {
            if ($subject->getVehicleMake() || $request->isMethod('POST')) {
                $formMapper->add('vehicleModel', EntityType::class, [
                    'placeholder' => '',
                    'class' => Model::class,
                    'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject) {
                        return $entityRepository->createQueryBuilder('m')
                            ->where('m.make = :make')
                            ->andWhere('m.active = :active')
                            ->setParameter('make', $subject->getVehicleMake())
                            ->setParameter('active', true)
                            ->orderBy('m.name', 'ASC');

                    },
                ]);
            }

            if ($subject->getVehicleModel() || $request->isMethod('POST')) {
                $formMapper->add('vehicleModelType', EntityType::class, [
                    'placeholder' => '',
                    'class' => ModelType::class,
                    'label' => 'Vehicle Trim',
                    'required' => false,
                    'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject) {
                        return $entityRepository->createQueryBuilder('m')
                            ->where('m.model = :model')
                            ->setParameter('model', $subject->getVehicleModel());
                    },
                ]);
            }
        }

        $formMapper->add('vehicleTransmission', WbcVehicleType\TransmissionType::class, ['required' => false])
            ->add('vehicleMileage', WbcVehicleType\MileageType::class)
            ->add('vehicleSpecifications', WbcVehicleType\SpecificationType::class, ['required' => false])
            ->add('vehicleBodyCondition', WbcVehicleType\ConditionType::class)
            ->end()
            ->end();

        $formMapper->tab('Customer Information')
            ->with('Customer Details')
            ->add('name')
            ->add('mobileNumber')
            ->add('emailAddress')
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

        if ($subject && $subject->getValuation()) {
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
                ->add('notes', TextareaType::class)
                ->end()
                ->end();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id')
            ->add('name')
            ->add('mobileNumber', null, ['label' => 'Mobile'])
            ->add('emailAddress')
            ->add('branchTiming.dayBooked', 'choice', ['choices' => DayType::getDays(), 'label' => 'Day Booked'])
            ->add('dateBooked')
            ->add('details.vehicleMakeName', null, ['label' => 'Make'])
            ->add('details.vehicleModelName', null, ['label' => 'Model'])
            ->add('status', 'choice', ['choices' => Appointment::getStatuses(), 'editable' => true])
            ->add('valuation.priceOnline', 'currency', ['currency' => 'AED'])
            ->add('createdAt', null, ['label' => 'Created'])
            ->add('updatedAt', null, ['label' => 'Updated'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
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
            ->add('listBranchTimings', 'branchTimings/{branchId}/{day}');
    }
}
