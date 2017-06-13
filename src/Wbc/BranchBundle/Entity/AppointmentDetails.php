<?php

namespace Wbc\BranchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AppointmentDetails.
 *
 * @ORM\Table(name="appointment_details")
 * @ORM\Entity
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class AppointmentDetails
{
    /**
     * @var Appointment
     *
     * @ORM\OneToOne(targetEntity="\Wbc\BranchBundle\Entity\Appointment", inversedBy="details")
     * @ORM\JoinColumn(name="appointment_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\Id
     *
     * @Assert\NotBlank()
     */
    protected $appointment;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_make_name", type="string", length=100)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $vehicleMakeName;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_model_name", type="string", length=100)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $vehicleModelName;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_model_type_name", type="string", length=100, nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $vehicleModelTypeName;

    /**
     * @var Branch
     *
     * @ORM\Column(name="branch", type="branch_object", nullable=true)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $branch;

    /**
     * @var Timing
     *
     * @ORM\Column(name="branch_timing", type="branch_timing_object", nullable=true)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $branchTiming;

    /**
     * AppointmentDetails Constructor.
     *
     * @param Appointment $appointment
     * @param Branch      $branch
     * @param Timing      $timing
     */
    public function __construct(Appointment $appointment, Branch $branch = null, Timing $timing = null)
    {
        $this->appointment = $appointment;
        $this->branch = $branch;
        $this->branchTiming = $timing;
    }

    /**
     * Set vehicleMakeName.
     *
     * @param string $vehicleMakeName
     *
     * @return AppointmentDetails
     */
    public function setVehicleMakeName($vehicleMakeName)
    {
        $this->vehicleMakeName = $vehicleMakeName;

        return $this;
    }

    /**
     * Get vehicleMakeName.
     *
     * @return string
     */
    public function getVehicleMakeName()
    {
        return $this->vehicleMakeName;
    }

    /**
     * Set vehicleModelName.
     *
     * @param string $vehicleModelName
     *
     * @return AppointmentDetails
     */
    public function setVehicleModelName($vehicleModelName)
    {
        $this->vehicleModelName = $vehicleModelName;

        return $this;
    }

    /**
     * Get vehicleModelName.
     *
     * @return string
     */
    public function getVehicleModelName()
    {
        return $this->vehicleModelName;
    }

    /**
     * Set branch.
     *
     * @param Branch $branch
     *
     * @return AppointmentDetails
     */
    public function setBranch(Branch $branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch.
     *
     * @return Branch
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * Set branchTiming.
     *
     * @param Timing $branchTiming
     *
     * @return AppointmentDetails
     */
    public function setBranchTiming(Timing $branchTiming)
    {
        $this->branchTiming = $branchTiming;

        return $this;
    }

    /**
     * Get branchTiming.
     *
     * @return Timing
     */
    public function getBranchTiming()
    {
        return $this->branchTiming;
    }

    /**
     * Set appointment.
     *
     * @param Appointment $appointment
     *
     * @return AppointmentDetails
     */
    public function setAppointment(Appointment $appointment)
    {
        $this->appointment = $appointment;

        return $this;
    }

    /**
     * Get appointment.
     *
     * @return Appointment
     */
    public function getAppointment()
    {
        return $this->appointment;
    }

    /**
     * Set vehicleModelTypeName.
     *
     * @param string $vehicleModelTypeName
     *
     * @return AppointmentDetails
     */
    public function setVehicleModelTypeName($vehicleModelTypeName)
    {
        $this->vehicleModelTypeName = $vehicleModelTypeName;

        return $this;
    }

    /**
     * Get vehicleModelTypeName.
     *
     * @return string
     */
    public function getVehicleModelTypeName()
    {
        return $this->vehicleModelTypeName;
    }
}
