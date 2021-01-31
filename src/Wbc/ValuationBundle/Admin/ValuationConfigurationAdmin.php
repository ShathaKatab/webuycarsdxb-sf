<?php

namespace Wbc\ValuationBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Wbc\VehicleBundle\Entity\Model;
use Wbc\VehicleBundle\Entity\ModelType;
use Wbc\VehicleBundle\Form as WbcVehicleType;

/**
 * Class ValuationConfigurationAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ValuationConfigurationAdmin extends AbstractAdmin
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

    protected function configureFormFields(FormMapper $form)
    {
        /** @var $subject \Wbc\BranchBundle\Entity\Appointment */
        $subject = $this->getSubject();
        $request = $this->getRequest();
        $authorizationChecker = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker');

        $form->add('vehicleYear', WbcVehicleType\ModelYearType::class, [
            'choice_label' => function ($value, $key, $index) {
                return $value;
            },
            'required' => false,
        ])
            ->add('vehicleMake', WbcVehicleType\MakeType::class, ['required' => false])
            ->add('vehicleModel', ChoiceType::class, ['required' => false, 'data' => ''])
            ->add('vehicleModelType', ChoiceType::class, ['required' => false, 'data' => ''])
            ->add('vehicleColor', WbcVehicleType\ColorType::class, ['required' => false])
            ->add('vehicleBodyCondition', WbcVehicleType\ConditionType::class, ['required' => false])
            ->add('discount', NumberType::class, ['label' => 'Price' , 'required' => false])
        ;
        if ($subject) {
            if ($subject->getVehicleMake() || $request->isMethod('POST')) {
                $form->add('vehicleModel', EntityType::class, ['placeholder' => '', 'class' => Model::class, 'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject) {
                    return $entityRepository->createQueryBuilder('m')->where('m.make = :make')->andWhere('m.active = :active')->setParameter('make', $subject->getVehicleMake())->setParameter('active', true)->orderBy('m.name', 'ASC');
                }]);

                $form->add('vehicleModelType', EntityType::class, ['placeholder' => '', 'class' => ModelType::class, 'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject) {
                    return $entityRepository->createQueryBuilder('mt')
                        ->innerJoin('mt.model', 'model')
                        ->where('model.make = :make')
                        ->andWhere('model.active = :active')
                        ->setParameter('make', $subject->getVehicleMake())
                        ->setParameter('active', true)
                        ->orderBy('mt.trim', 'ASC');
                }]);
            }
        }

        $form->add('active', null, ['disabled' => !$authorizationChecker->isGranted('ROLE_VALUATION_CONFIGURATION_ACTIVATOR')]);
    }

    protected function configureListFields(ListMapper $list)
    {
        $authorizationChecker = $this->getConfigurationPool()->getContainer()->get('security.authorization_checker');

        $list->add('vehicleYear')
            ->add('vehicleMake', null, array('sortable' => true, 'sort_field_mapping' => array('fieldName' => 'name'), 'sort_parent_association_mappings' => array(array('fieldName' => 'vehicleMake'))))
            ->add('vehicleModel', null, array('sortable' => true, 'sort_field_mapping' => array('fieldName' => 'name'), 'sort_parent_association_mappings' => array(array('fieldName' => 'vehicleModel'))))
            ->add('vehicleModelType', null, array('sortable' => true, 'sort_field_mapping' => array('fieldName' => 'name'), 'sort_parent_association_mappings' => array(array('fieldName' => 'vehicleModelType'))))
            ->add('vehicleColor')
            ->add('vehicleBodyCondition')
            ->add('discount', NumberType::class, ['label' => 'price'])
            ->add('active', 'boolean', ['editable' => $authorizationChecker->isGranted('ROLE_VALUATION_CONFIGURATION_ACTIVATOR')])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => [], 'delete' => []]]);
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show->add('vehicleYear')
            ->add('vehicleMake')
            ->add('vehicleModel')
            ->add('vehicleModelType')
            ->add('vehicleColor')
            ->add('vehicleBodyCondition')
            ->add('discount', NumberType::class, ['label' => 'price'])
            ->add('active', 'boolean');
    }
}
