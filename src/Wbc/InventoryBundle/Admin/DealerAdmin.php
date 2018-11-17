<?php

declare(strict_types=1);

namespace Wbc\InventoryBundle\Admin;

use Wbc\UtilityBundle\Form\MobileNumberType;
use Wbc\UtilityBundle\Form\PhoneNumberType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Wbc\InventoryBundle\Entity\Dealer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class DealerAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class DealerAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form->tab('Dealer')
            ->with('')
            ->add('type', ChoiceType::class, ['choices' => Dealer::getTypes(), 'placeholder' => '--Select Type--'])
            ->add('emailAddress', EmailType::class, ['required' => false])
            ->add('address', TextareaType::class, ['required' => false])
            ->add('active', CheckboxType::class, ['required' => false])
            ->end()
            ->with('Retail (Fill in if the type is Retail)')
            ->add('name', TextType::class, ['required' => false, 'label' => 'Customer Name'])
            ->add('mobileNumber', MobileNumberType::class, ['required' => false])
            ->add('emiratesId', TextType::class, ['required' => false])
            ->add('imageEmiratesId', 'sonata_type_model_list', ['required' => false])
            ->end()
            ->with('Dealer (Fill in if the type is Dealer)')->add('nameCompany', TextType::class, ['required' => false, 'label' => 'Name of Dealer'])
            ->add('telephoneNumber', PhoneNumberType::class, ['required' => false])
            ->add('numberTradeLicense', TextType::class, ['required' => false, 'label' => 'Trade License Number'])
            ->add('imageTradeLicense', 'sonata_type_model_list', ['required' => false])
            ->end()
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('id')
            ->add('name', TextType::class, ['required' => false, 'label' => 'Customer Name'])
            ->add('mobileNumber', MobileNumberType::class, ['required' => false])
            ->add('telephoneNumber', PhoneNumberType::class, ['required' => false])
            ->add('emailAddress', EmailType::class, ['required' => false])
            ->add('nameCompany', TextType::class, ['required' => false, 'label' => 'Name of Dealer'])
            ->add('type', 'choice', ['choices' => Dealer::getTypes()])
            ->add('active', 'boolean')
            ->add('_action', 'actions', ['actions' => ['show' => [], 'edit' => []]]);
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show->tab('Dealer')
            ->with('')
            ->add('type', ChoiceType::class, ['choices' => array_flip(Dealer::getTypes()), 'placeholder' => '--Select Type--'])
            ->add('emailAddress', EmailType::class)
            ->add('address', TextareaType::class)
            ->add('active', 'boolean')
            ->end()
            ->with('Retail')
            ->add('name', TextType::class, ['label' => 'Customer Name'])
            ->add('mobileNumber', MobileNumberType::class)
            ->add('emiratesId', TextType::class)
            ->add('imageEmiratesId', 'sonata_type_model_list')
            ->end()
            ->with('Dealer')
            ->add('nameCompany', TextType::class, ['required' => false, 'label' => 'Name of Dealer'])
            ->add('telephoneNumber', PhoneNumberType::class)
            ->add('numberTradeLicense', TextType::class, ['required' => false, 'label' => 'Trade License Number'])
            ->add('imageTradeLicense', 'sonata_type_model_list')
            ->end()
            ->end();
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }
}
