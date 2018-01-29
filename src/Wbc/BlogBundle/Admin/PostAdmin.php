<?php

namespace Wbc\BlogBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\DateTimePickerType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;

class PostAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('slug')
            ->add('enabled')
            ->add('publicationDateStart')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('enabled', 'boolean', ['editable' => true])
            ->add('categories')
            ->add('publicationDateStart')
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
        $formMapper->with('Blog', ['class' => 'col-md-9'])
            ->add('title')
            ->add('slug')
            ->add('content', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default',
            ])
            ->add('metaTitle')
            ->add('metaDescription')
            ->end()
            ->with('Extras', ['class' => 'col-md-3'])
            ->add('author')
            ->add('enabled')
            ->add('publicationDateStart', DateTimePickerType::class)
            ->add('imageAlt')
            ->add('image', 'sonata_type_model_list', [], ['link_parameters' => ['context' => 'default']])
            ->add('categories')
            ->end()
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('content')
            ->add('metaTitle')
            ->add('metaDescription')
            ->add('slug')
            ->add('enabled')
            ->add('publicationDateStart')
            ->add('imageAlt')
            ->add('image')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }
}
