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
     * AppointmentDetails Constructor.
     *
     * @param Appointment $appointment
     * @param Branch|null $branch
     */
    public function __construct(Appointment $appointment, Branch $branch = null)
    {
        $this->setAppointment($appointment);
        $this->branch = $branch;
    }

    /**
     * Set vehicleMakeName.
     *
     * @param string $vehicleMakeName
     *
     * @return AppointmentDetails
     */
    public function setVehicleMakeName(string $vehicleMakeName)
    {
        $this->vehicleMakeName = $vehicleMakeName;

        return $this;
    }

    /**
     * Get vehicleMakeName.
     *
     * @return string|null
     */
    public function getVehicleMakeName(): ?string
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
    public function setVehicleModelName(string $vehicleModelName)
    {
        $this->vehicleModelName = $vehicleModelName;

        return $this;
    }

    /**
     * Get vehicleModelName.
     *
     * @return string|null
     */
    public function getVehicleModelName(): ?string
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
     * @return Branch|null
     */
    public function getBranch(): ?Branch
    {
        return $this->branch;
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
        $this->appointment->setDetails($this);

        return $this;
    }

    /**
     * Get appointment.
     *
     * @return Appointment|null
     */
    public function getAppointment(): ?Appointment
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
    public function setVehicleModelTypeName(string $vehicleModelTypeName)
    {
        $this->vehicleModelTypeName = $vehicleModelTypeName;

        return $this;
    }

    /**
     * Get vehicleModelTypeName.
     *
     * @return string|null
     */
    public function getVehicleModelTypeName(): ?string
    {
        return $this->vehicleModelTypeName;
    }
}
