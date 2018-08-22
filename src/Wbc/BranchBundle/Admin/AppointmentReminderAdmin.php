<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Wbc\BranchBundle\Entity\AppointmentReminder;
use Wbc\UtilityBundle\AdminDateRange;
use Wbc\VehicleBundle\Form as WbcVehicleType;

/**
 * Class AppointmentReminderAdmin.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class AppointmentReminderAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_per_page' => 25,
        '_sort_order' => 'ASC',
        '_sort_by' => 'createdAt',
        'status' => ['value' => AppointmentReminder::STATUS_NEW],
        'isReschedule' => ['value' => true],
    ];

    /**
     * {@inheritdoc}
     */
    public function getExportFormats()
    {
        return ['xls'];
    }

    protected function configureFormFields(FormMapper $form)
    {
        /** @var $subject \Wbc\BranchBundle\Entity\AppointmentReminder */
        $subject = $this->getSubject();

        $form->tab('Appointment Reminder Details')
            ->with('Vehicle Details')
            ->add('appointment.vehicleYear', WbcVehicleType\ModelYearType::class, ['disabled' => true, 'required' => false])
            ->add('appointment.vehicleMake', WbcVehicleType\MakeType::class, ['disabled' => true, 'required' => false])
            ->add('appointment.vehicleModel', null, ['required' => false, 'disabled' => true])
            ->add('appointment.vehicleModelType', null, ['disabled' => true, 'required' => false])
            ->end()
            ->with('Other Details')
            ->add('isReschedule', CheckboxType::class, ['disabled' => true, 'required' => false, 'label' => 'Requested Callback?'])
            ->add('mobileNumber', TextType::class, ['disabled' => true, 'required' => false])
            ->add('responseText', TextareaType::class, ['disabled' => true, 'required' => false])
            ->add('status', ChoiceType::class, ['choices' => AppointmentReminder::getStatuses()])
            ->end()
            ->end();
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('appointment.name')
            ->add('appointment.vehicleYear', null, ['label' => 'Vehicle Year'])
            ->add('appointment.vehicleMake', null, ['label' => 'Vehicle Make'])
            ->add('appointment.vehicleModel', null, ['label' => 'Vehicle Model'])
            ->add('mobileNumber')
            ->add('isReschedule', 'boolean', ['label' => 'Requested Callback?'])
            ->add('status', 'choice', ['choices' => AppointmentReminder::getStatuses(), 'editable' => true])
            ->add('createdAt')
            ->add('_action', 'actions', [
                    'actions' => [
                        'show' => [],
                        'edit' => [],
                        'appointment' => ['template' => 'WbcBranchBundle:Admin/CRUD:list__action_go_to_appointment.html.twig'],
                    ],
                ]
            );
    }

    protected function configureShowFields(ShowMapper $show)
    {
        $show->tab('Appointment Reminder Details')
            ->with('Vehicle Details')
            ->add('appointment.vehicleYear', WbcVehicleType\ModelYearType::class, ['disabled' => true, 'required' => false])
            ->add('appointment.vehicleMake', WbcVehicleType\MakeType::class, ['disabled' => true, 'required' => false])
            ->add('appointment.vehicleModel', null, ['required' => false, 'disabled' => true])
            ->add('appointment.vehicleModelType', null, ['disabled' => true, 'required' => false])
            ->end()
            ->with('Other Details')
            ->add('isReschedule', CheckboxType::class, ['disabled' => true, 'required' => false, 'label' => 'Requested Callback?'])
            ->add('mobileNumber', TextType::class, ['disabled' => true, 'required' => false])
            ->add('responseText', TextareaType::class, ['disabled' => true, 'required' => false])
            ->add('status', 'choice', ['choices' => AppointmentReminder::getStatuses()])
            ->add('createdAt')
            ->add('updatedAt')
            ->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $now = new \DateTime();

        $datagridMapper->add('appointment.name')
            ->add('mobileNumber')
            ->add('isReschedule', 'doctrine_orm_boolean', ['label' => 'Requested Callback?'])
            ->add('status', 'doctrine_orm_choice', [
                'field_options' => [
                    'choices' => AppointmentReminder::getStatuses(),
                ],
                'field_type' => 'choice',
            ])
            ->add('createdAt', 'doctrine_orm_date_range', AdminDateRange::getDoctrineOrmDateRange('Date Created At'))
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create')->remove('delete');
    }
}
