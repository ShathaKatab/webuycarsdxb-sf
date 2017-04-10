<?php

namespace Wbc\BranchBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Wbc\UtilityBundle\Form\MobileNumberType;

/**
 * Class BranchAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class BranchAdmin extends Admin
{
    /**
     * Fields to be shown on create/edit forms.
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name')
            ->add('latitude', TextType::class, [
                'help' => 'e.g. 25.098875',
            ])
            ->add('longitude', TextType::class, [
                'help' => 'e.g. 55.204752',
            ])
            ->add('address', TextareaType::class, [
                'help' => 'e.g. Ground Floor, 23rd Street, Al Barsha 2 (Al Barsha Mall)',
            ])
            ->add('citySlug', ChoiceType::class, [
                'label' => 'City',
                'required' => false,
                'choices' => [
                    'dubai' => 'Dubai',
                    'abu-dhabi' => 'Abu Dhabi',
                    'al-ain' => 'Al Ain',
                    'sharjah' => 'Sharjah',
                    'ajman' => 'Ajman',
                    'umm-al-quwain' => 'Umm Al Quwain',
                    'ras-al-khaimah' => 'Ras Al Khaimah',
                ],
            ])
            ->add('phoneNumber', MobileNumberType::class, [
                'help' => 'e.g. 041234567',
            ]);
    }

    /**
     * Fields to be shown on filter forms.
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    /**
     * Fields to be shown on lists.
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name')
            ->add('citySlug', 'choice', [
                'label' => 'City',
                'choices' => [
                    'dubai' => 'Dubai',
                    'abu-dhabi' => 'Abu Dhabi',
                    'al-ain' => 'Al Ain',
                    'sharjah' => 'Sharjah',
                    'ajman' => 'Ajman',
                    'umm-al-quwain' => 'Umm Al Quwain',
                    'ras-al-khaimah' => 'Ras Al Khaimah',
                ],
            ])
            ->add('phoneNumber')
            ->add('active', 'boolean', ['editable' => true])
            ->add('createdAt')
            ->add('updatedAt');
    }

    /**
     * Fields to be shown on show action.
     *
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name')
            ->add('latitude')
            ->add('longitude')
            ->add('address')
            ->add('citySlug', 'choice', [
                'label' => 'City',
                'choices' => [
                    'dubai' => 'Dubai',
                    'abu-dhabi' => 'Abu Dhabi',
                    'al-ain' => 'Al Ain',
                    'sharjah' => 'Sharjah',
                    'ajman' => 'Ajman',
                    'umm-al-quwain' => 'Umm Al Quwain',
                    'ras-al-khaimah' => 'Ras Al Khaimah',
                ], ])
            ->add('phoneNumber');
    }
}
