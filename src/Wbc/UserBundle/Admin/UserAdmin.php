<?php

namespace Wbc\UserBundle\Admin;

use FOS\UserBundle\Model\UserManager;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Wbc\UserBundle\Entity\User;
use Wbc\UtilityBundle\Form\MobileNumberType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * Class UserAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class UserAdmin extends AbstractAdmin
{
    protected $datagridValues = ['_page' => 1, '_per_page' => 25, '_sort_order' => 'DESC', '_sort_by' => 'createdAt',];

    /**
     * @param User        $object
     * @param UserManager $manager
     *
     * @return mixed
     */
    public function createUserObject($object, UserManager $manager)
    {
        $this->prePersist($object);

        foreach ($this->extensions as $extension) {
            $extension->prePersist($this, $object);
        }

        $result = $manager->updateUser($object, true);

        // BC compatibility
        if (null !== $result) {
            $object = $result;
        }

        $this->postPersist($object);

        foreach ($this->extensions as $extension) {
            $extension->postPersist($this, $object);
        }

        $this->createObjectSecurity($object);

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();

        $formMapper->add('email', EmailType::class, ['label' => 'Email Address'])
            ->add('profile.firstName', TextType::class, ['label' => 'First Name'])
            ->add('profile.lastName', null, ['label' => 'Last Name'])
            ->add('profile.mobileNumber', MobileNumberType::class, [
                'help' => 'e.g. 0551234567',
                'label' => 'Mobile',
                'attr' => ['autocomplete' => 'off'],
            ]);

        if (!$subject || ($subject && !$subject->getId())) {
            $formMapper->add('plainPassword', PasswordType::class, [
                'attr' => ['autocomplete' => 'new-password',
                ],
                'label' => 'Password',
            ]);
        }

        $formMapper->add('enabled', CheckboxType::class, [
            'data' => true,
            'label' => 'Enable?',
            'required' => false,
        ])
            ->add('roles', ChoiceType::class, [
                'choices' => ['ROLE_SUPER_ADMIN' => 'Super Admin', 'ROLE_BUYER' => 'Buyer'],
                'expanded' => false,
                'multiple' => true,
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('email')->add('profile.mobileNumber', null, ['label' => 'Mobile Number']);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('id')
            ->add('email')
            ->add('profile.firstName', null, ['label' => 'First Name'])
            ->add('profile.lastName', null, ['label' => 'Last Name'])
            ->add('profile.mobileNumber', null, ['label' => 'Mobile'])
            ->add('enabled', 'boolean', ['editable' => true])
            ->add('lastLogin')
            ->add('isAdmin', 'boolean', ['editable' => true, 'label' => 'Super Admin?'])
            ->add('createdAt')
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'change_password' => ['template' => 'WbcUserBundle:Admin:list__action_change_password.html.twig'],
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('email', null, ['label' => 'Email Address'])
            ->add('profile.firstName', null, ['label' => 'First Name'])
            ->add('profile.lastName', null, ['label' => 'Last Name'])
            ->add('profile.mobileNumber', MobileNumberType::class, ['label' => 'Mobile'])
            ->add('enabled', 'choice', [
                'label' => 'Account Active?',
                'choices' => [1 => 'yes', 0 => 'no'],
            ])
            ->add('admin', 'choice', [
                'choices' => [0 => 'no', 1 => 'yes'],
                'label' => 'Super Admin?', ])
            ->add('lastLogin')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('changePassword', sprintf('%s/changePassword', $this->getRouterIdParameter()));
    }
}
