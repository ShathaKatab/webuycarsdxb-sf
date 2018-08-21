<?php

declare(strict_types=1);

namespace Wbc\InventoryBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Wbc\VehicleBundle\Entity\Model;
use Wbc\VehicleBundle\Entity\ModelType;
use Wbc\VehicleBundle\Form as WbcVehicleType;

/**
 * Class UsedCarAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class UsedCarAdmin extends AbstractAdmin
{
    protected $datagridValues = ['_page' => 1, '_per_page' => 25, '_sort_order' => 'DESC', '_sort_by' => 'createdAt'];

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
            default:
                return parent::getTemplate($name);
        }
    }

    public function prePersist($object): void
    {
        $tokenStorage = $this->getConfigurationPool()->getContainer()->get('security.token_storage');

        if ($tokenStorage->getToken()) {
            $object->setCreatedBy($tokenStorage->getToken()->getUser());
        }
    }

    protected function configureFormFields(FormMapper $form): void
    {
        /** @var $subject \Wbc\InventoryBundle\Entity\UsedCar */
        $subject = $this->getSubject();
        $request = $this->getRequest();

        $form->tab('Vehicle Information')
            ->with('Vehicle Details')
            ->add('inventory.year', WbcVehicleType\ModelYearType::class, ['disabled' => true])
            ->add('inventory.make', WbcVehicleType\MakeType::class, ['disabled' => true])
            ->add('inventory.model', EntityType::class, [
                'disabled' => true,
                'placeholder' => '',
                'class' => Model::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
                }, ])
            ->add('inventory.modelType', EntityType::class, [
                'disabled' => true,
                'required' => false,
                'placeholder' => '',
                'class' => ModelType::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
                }, ]);

        if ($subject) {
            $inventory = $subject->getInventory();

            $vehicleMake = $inventory->getMake();
            $vehicleModel = $inventory->getModel();

            if ($vehicleMake || $request->isMethod('POST')) {
                $form->add('inventory.model', EntityType::class, [
                    'disabled' => true,
                    'placeholder' => '',
                    'class' => Model::class,
                    'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject, $vehicleMake) {
                        return $entityRepository->createQueryBuilder('m')
                            ->where('m.make = :make')
                            ->andWhere('m.active = :active')
                            ->setParameter('make', $vehicleMake)
                            ->setParameter('active', true)->orderBy('m.name', 'ASC');
                    }, ]);
            }

            if ($vehicleModel || $request->isMethod('POST')) {
                $form->add('inventory.modelType', EntityType::class, [
                    'placeholder' => '',
                    'disabled' => true,
                    'class' => ModelType::class,
                    'label' => 'Inventory Vehicle Trim',
                    'required' => false,
                    'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject, $vehicleModel) {
                        return $entityRepository->createQueryBuilder('m')
                            ->where('m.model = :model')
                            ->setParameter('model', $vehicleModel);
                    }, ]);
            }
        }

        $form->add('inventory.transmission', WbcVehicleType\TransmissionType::class, ['required' => false, 'disabled' => true])
            ->add('inventory.mileage', WbcVehicleType\MileageType::class, ['disabled' => true])
            ->add('inventory.specifications', WbcVehicleType\SpecificationType::class, ['required' => false, 'disabled' => true])
            ->add('inventory.bodyCondition', WbcVehicleType\ConditionType::class, ['required' => false, 'disabled' => true])
            ->add('mechanicalCondition', WbcVehicleType\ConditionType::class, ['required' => false])
            ->add('inventory.options', ChoiceType::class, ['required' => false, 'choices' => ['basic' => 'Basic', 'mid' => 'Mid', 'full' => 'Full'], 'disabled' => true])
            ->add('bodyType', ChoiceType::class, ['required' => false, 'choices' => ['coupe' => 'Coupe', 'crossover' => 'Crossover', 'hard-top-convertible' => 'Hard Top Convertible', 'hatchback' => 'Hatchback', 'pick-up' => 'Pick Up', 'sedan' => 'Sedan', 'soft-top-convertible' => 'Soft Top Convertible', 'sports-car' => 'Sports Car', 'suv' => 'SUV', 'utility-truck' => 'Utility Truck', 'van' => 'Van', 'wagon' => 'Wagon', 'other' => 'Other']])
            ->add('inventory.color', WbcVehicleType\ColorType::class, ['disabled' => true])
            ->add('doors', IntegerType::class, ['required' => false])
            ->add('cylinders', IntegerType::class, ['required' => false])
            ->add('horsepower', IntegerType::class, ['required' => false])
            ->end()
            ->end();

        $form->tab('Listing Information')
            ->add('description', TextareaType::class, ['required' => false])
            ->add('price', MoneyType::class, ['currency' => 'AED'])
            ->add('active', CheckboxType::class, ['required' => false])
            ->end()
            ->end();

        $form->tab('Images Information')
            ->add('gallery', 'sonata_type_model_list', ['btn_list' => false, 'by_reference' => false],
                [
                    'link_parameters' => [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                        'context' => 'default',
                        'provider' => 'sonata.media.provider.image',
                    ],
                ])
            ->end();

        $form->end();
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id')
            ->add('inventory.make')
            ->add('inventory.model')
            ->add('inventory.modelType')
            ->add('inventory.mileage', null, ['label' => 'Mileage (Kms)'])
            ->add('price', null, ['label' => 'Price (AED)'])
            ->add('hasImages', 'boolean')
            ->add('active', 'boolean', ['editable' => true])
            ->add('inventory.deal')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ], ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->tab('Vehicle Information')
            ->with('Vehicle Details')
            ->add('inventory.year', WbcVehicleType\ModelYearType::class)
            ->add('inventory.make', WbcVehicleType\MakeType::class)
            ->add('inventory.model', EntityType::class, ['placeholder' => '', 'class' => Model::class, 'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
            }])
            ->add('inventory.modelType', EntityType::class, ['required' => false, 'placeholder' => '', 'class' => ModelType::class, 'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('m')->where('m.id IS NULL'); //don't populate anything
            }]);
        $show->add('inventory.transmission', WbcVehicleType\TransmissionType::class)
            ->add('inventory.mileage', WbcVehicleType\MileageType::class)
            ->add('inventory.specifications', WbcVehicleType\SpecificationType::class)
            ->add('inventory.bodyCondition', WbcVehicleType\ConditionType::class)
            ->add('mechanicalCondition', WbcVehicleType\ConditionType::class)
            ->add('inventory.options', ChoiceType::class, ['choices' => ['basic' => 'Basic', 'mid' => 'Mid', 'full' => 'Full']])
            ->add('inventory.bodyType', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'coupe' => 'Coupe',
                    'crossover' => 'Crossover',
                    'hard-top-convertible' => 'Hard Top Convertible',
                    'hatchback' => 'Hatchback',
                    'pick-up' => 'Pick Up',
                    'sedan' => 'Sedan',
                    'soft-top-convertible' => 'Soft Top Convertible',
                    'sports-car' => 'Sports Car',
                    'suv' => 'SUV',
                    'utility-truck' => 'Utility Truck',
                    'van' => 'Van', 'wagon' => 'Wagon',
                    'other' => 'Other',
                ], ])
            ->add('inventory.color', WbcVehicleType\ColorType::class)
            ->add('doors', IntegerType::class)
            ->add('cylinders', IntegerType::class)
            ->add('horsepower', IntegerType::class)
            ->end()
            ->end();
        $show->tab('Listing Information')
            ->with('Listing')
            ->add('description', TextareaType::class)
            ->add('price', MoneyType::class, ['currency' => 'AED'])
            ->add('active', CheckboxType::class)
            ->end()
            ->end();
        $show->tab('Images Information')
            ->with('Images')
            ->add('gallery')
            ->end();
        $show->end();
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->remove('create');
    }
}
