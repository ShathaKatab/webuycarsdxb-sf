<?php

declare(strict_types=1);

namespace Wbc\CareersBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Wbc\CareersBundle\Entity\Candidate;
use Wbc\CareersBundle\Entity\Role;
use Wbc\UtilityBundle\Form\MobileNumberType;

/**
 * Class CandidateAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class CandidateAdmin extends AbstractAdmin
{
    private $roles;

    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('firstName')
            ->add('lastName')
            ->add('emailAddress', EmailType::class)
            ->add('mobileNumber', MobileNumberType::class)
            ->add('currentRole', TextType::class, ['required' => false])
            ->add('coverLetter', TextareaType::class, ['required' => false])
            ->add('uploadedCv', 'sonata_type_model_list', [], ['link_parameters' => ['context' => 'default']])
            ->add('status', ChoiceType::class, ['choices' => array_flip(Candidate::getStatuses())])
            ->add('interviewAt', DatePickerType::class, ['required' => false])
            ->add('role')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id')
            ->add('firstName')
            ->add('lastName')
            ->add('emailAddress')
            ->add('mobileNumber')
            ->add('role.title')
            ->add('status', 'choice', ['choices' => Candidate::getStatuses()])
            ->add('uploadedCv',null, ['template' => 'WbcCareersBundle:Admin/CRUD:list__uploaded_cv.html.twig'])

            ->add('_action', null, ['actions' => ['show' => [], 'edit' => [], 'delete' => []]]);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $roles = $this->getRoles();

        $filter->add('firstName')
            ->add('lastName')
            ->add('emailAddress')
            ->add('mobileNumber')
            ->add('role', 'doctrine_orm_callback', [
                'callback' => function ($queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return false;
                    }
                    $queryBuilder->andWhere($alias.'.role = :role')
                        ->setParameter(':role', $queryBuilder->getEntityManager()->getReference(Role::class, $value['value']));

                    return true;
                },
                'field_type' => 'choice',
                'field_options' => ['choices' => $roles], ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('firstName')
            ->add('lastName')
            ->add('emailAddress')
            ->add('mobileNumber')
            ->add('currentRole')
            ->add('coverLetter')
            ->add('uploadedCv', null, ['template' => 'WbcCareersBundle:Admin/CRUD:show__uploaded_cv.html.twig'])
            ->add('status', 'choice', ['choices' => Candidate::getStatuses()])
            ->add('interviewAt')
            ->add('role');
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        parent::configureRoutes($collection);
    }

    private function getRoles()
    {
        if (!$this->roles) {
            $this->roles = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.default_entity_manager')
                ->getRepository(Role::class)
                ->findByEntityForAdminFilters();
        }

        return $this->roles;
    }
}
