<?php

namespace Wbc\BlogBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     */
    public function getTemplate($name)
    {
        switch ($name) {
            case 'create':
            case 'edit':
                return 'WbcBlogBundle:Admin:edit.html.twig';
            default:
                return parent::getTemplate($name);
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('slug')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('slug')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var \Wbc\BlogBundle\Entity\Category $subject */
        $subject = $this->getSubject();

        $formMapper
            ->add('name', TextType::class, [
                'attr' => [
                    'ng-model' => 'ctrl.title',
                    'ng-blur' => 'ctrl.titleChanged()',
                    'ng-init' => sprintf('ctrl.name = "%s"; ctrl.slug = "%s";', $subject->getName(), $subject->getSlug()),
                ],
            ])
            ->add('slug', TextType::class, ['attr' => ['ng-model' => 'ctrl.slug']])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('slug')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }
}
