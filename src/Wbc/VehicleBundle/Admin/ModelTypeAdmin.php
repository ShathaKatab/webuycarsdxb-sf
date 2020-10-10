<?php

namespace Wbc\VehicleBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Wbc\VehicleBundle\Entity\Model;
use Wbc\VehicleBundle\Entity\ModelType;
use Wbc\VehicleBundle\Form\BodyType;
use Wbc\VehicleBundle\Form\MakeType;
use Wbc\VehicleBundle\Form\ModelYearType;
use Wbc\VehicleBundle\Form\TransmissionType;

/**
 * Class ModelTypeAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ModelTypeAdmin extends AbstractAdmin
{
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
    protected function configureFormFields(FormMapper $form)
    {
        /**@var ModelType $subject */
        $subject = $this->getSubject();
        $request = $this->getRequest();

        $form->add('trim', TextType::class, ['label' => 'Trim Name'])
            ->add('make', MakeType::class)
            ->add('model', ChoiceType::class);

        if ($subject !== null && ($subject->getMake() !== null || $request->isMethod('POST'))) {
                $form->add('model', EntityType::class, ['placeholder' => '', 'class' => Model::class, 'query_builder' => $request->isMethod('POST') ? null : function (EntityRepository $entityRepository) use ($subject) {
                    return $entityRepository->createQueryBuilder('m')->where('m.make = :make')->andWhere('m.active = :active')->setParameter('make', $subject->getMake())->setParameter('active', true)->orderBy('m.name', 'ASC');
                }]);
        }

        $form->add('bodyType', BodyType::class)
            ->add('engine', IntegerType::class, ['help' => 'e.g. 2500 for a 2.5 litre engine'])
            ->add('transmission', TransmissionType::class)
            ->add('cylinders', IntegerType::class, ['required' => false])
            ->add('seats', IntegerType::class, ['required' => false])
            ->add('isGcc', CheckboxType::class, ['required' => false])
            ->add('years', ModelYearType::class, ['multiple' => true, 'required' => false])
            ->add('enabled', CheckboxType::class, ['required' => false])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id')
            ->add('trim')
            ->add('make')
            ->add('model')
            ->add('bodyType')
            ->add('engine')
            ->add('cylinders')
            ->add('transmission')
            ->add('seats')
            ->add('isGcc')
            ->add('enabled', 'boolean', ['editable' => true])
            ->add('flattenedYears', null, ['label' => 'Years'])
            ->add('createdAt')
            ->add('updatedAt')
            ->add('_action', 'actions', [
                'actions' => ['show' => [], 'edit' => [], 'delete' => []], ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('trim')->add('model')->add('isGcc')->add('enabled');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show->add('id')
            ->add('trim')
            ->add('model')
            ->add('engine')
            ->add('cylinders')
            ->add('transmission')
            ->add('seats')
            ->add('isGcc')
            ->add('flattenedYears', null, ['label' => 'Years'])
            ->add('enabled')
            ->add('createdAt')
            ->add('updatedAt');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['create', 'edit', 'show', 'list']);
    }
}
