<?php

namespace Wbc\BranchBundle\Events;

use Symfony\Component\EventDispatcher\Event;
use Wbc\BranchBundle\Entity\Appointment;

/**
 * Class AppointmentEvent.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class AppointmentEvent extends Event
{
    private $appointment;

    /**
     * AppointmentEvent Constructor.
     *
     * @param Appointment $appointment
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * @return Appointment
     */
    public function getAppointment()
    {
        return $this->appointment;
    }

    /**
     * @param Appointment $appointment
     */
    public function setAppointment(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }
}
