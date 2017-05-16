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

        $form->add('vehicleYear', WbcVehicleType\ModelYearType::class, [
            'choice_label' => function ($value, $key, $index) {
                return $value;
            },
            'required' => false,
        ])
            ->add('vehicleMake', WbcVehicleType\MakeType::class, ['required' => false,])
            ->add('vehicleModel', ChoiceType::class, ['required' => false, 'data' => ''])
            ->add('discount', NumberType::class, ['label' => 'Discount (%)'])
        ;
        if ($subject) {
            if ($subject->getVehicleMake() || $request->isMethod('POST')) {
                $form->add('vehicleModel', EntityType::class, ['placeholder' => '', 'class' => Model::class, 'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject) {
                    return $entityRepository->createQueryBuilder('m')->where('m.make = :make')->andWhere('m.active = :active')->setParameter('make', $subject->getVehicleMake())->setParameter('active', true)->orderBy('m.name', 'ASC');

                }]);
            }
        }
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->add('vehicleYear')
            ->add('vehicleMake')
            ->add('vehicleModel')
            ->add('discount', NumberType::class, ['label' => 'Discount (%)'])
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => [], 'delete' => []]]);
        ;
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show->add('vehicleYear')
            ->add('vehicleMake')
            ->add('vehicleModel')
            ->add('discount', NumberType::class, ['label' => 'Discount (%)']);
    }
}
