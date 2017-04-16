<?php

namespace Wbc\BranchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Wbc\VehicleBundle\Entity\Make;
use Wbc\VehicleBundle\Entity\Model;
use Wbc\VehicleBundle\Entity\ModelType;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Appointment.
 *
 * @ORM\Table(name="appointment")
 * @ORM\Entity
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class Appointment
{
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_OFFER_ACCEPTED = 'offer_accepted';
    const STATUS_OFFER_REJECTED = 'offer_rejected';
    const STATUS_INVALID = 'invalid';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, unique=true)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_number", type="string", length=15)
     *
     * @Assert\NotBlank()
     */
    protected $mobileNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="email_address", type="string", length=100)
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $emailAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality", type="string", length=2, nullable=true)
     *
     * @Assert\Length(min=2, max=2)
     */
    protected $nationality;

    /**
     * @var Make
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\VehicleBundle\Entity\Make")
     * @ORM\JoinColumn(name="vehicle_make_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    protected $vehicleMake;

    /**
     * @var Model
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\VehicleBundle\Entity\Model")
     * @ORM\JoinColumn(name="vehicle_model_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    protected $vehicleModel;

    /**
     * @var ModelType
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\VehicleBundle\Entity\ModelType")
     * @ORM\JoinColumn(name="vehicle_model_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    protected $vehicleModelType;

    /**
     * @var int
     *
     * @ORM\Column(name="vehicle_year", type="smallint")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $vehicleYear;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_transmission", type="string", length=15)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $vehicleTransmission;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_trim", type="string", length=100)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $vehicleTrim;

    /**
     * @var int
     *
     * @ORM\Column(name="vehicle_mileage", type="bigint")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $vehicleMileage;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_specifications", type="string", length=10)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $vehicleSpecifications;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_body_condition", type="string", length=30)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $vehicleBodyCondition;

    /**
     * @var Branch
     */
    protected $branch;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_booked", type="date")
     */
    protected $dateBooked;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $to;

    /**
     * @var Timing
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\BranchBundle\Entity\Timing")
     * @ORM\JoinColumn(name="branch_timing", referencedColumnName="id", onDelete="SET NULL", nullable=true)})
     *
     *
     * @Serializer\Expose()
     */
    protected $branchTiming;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20)
     */
    protected $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @Serializer\Expose()
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     *
     * @Serializer\Expose()
     */
    protected $updatedAt;

    /**
     * @var AppointmentDetails
     *
     * @ORM\OneToOne(targetEntity="\Wbc\BranchBundle\Entity\AppointmentDetails", mappedBy="appointment")
     *
     * @Serializer\Expose()
     */
    protected $details;

    /**
     * Appointment Constructor.
     */
    public function __construct()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Appointment
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set mobileNumber.
     *
     * @param string $mobileNumber
     *
     * @return Appointment
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    /**
     * Get mobileNumber.
     *
     * @return string
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * Set emailAddress.
     *
     * @param string $emailAddress
     *
     * @return Appointment
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get emailAddress.
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set nationality.
     *
     * @param string $nationality
     *
     * @return Appointment
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Get nationality.
     *
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set vehicleTrim.
     *
     * @param string $vehicleTrim
     *
     * @return Appointment
     */
    public function setVehicleTrim($vehicleTrim)
    {
        $this->vehicleTrim = $vehicleTrim;

        return $this;
    }

    /**
     * Get vehicleTrim.
     *
     * @return string
     */
    public function getVehicleTrim()
    {
        return $this->vehicleTrim;
    }

    /**
     * Set vehicleSpecifications.
     *
     * @param string $vehicleSpecifications
     *
     * @return Appointment
     */
    public function setVehicleSpecifications($vehicleSpecifications)
    {
        $this->vehicleSpecifications = $vehicleSpecifications;

        return $this;
    }

    /**
     * Get vehicleSpecifications.
     *
     * @return string
     */
    public function getVehicleSpecifications()
    {
        return $this->vehicleSpecifications;
    }

    /**
     * Set vehicleBodyCondition.
     *
     * @param string $vehicleBodyCondition
     *
     * @return Appointment
     */
    public function setVehicleBodyCondition($vehicleBodyCondition)
    {
        $this->vehicleBodyCondition = $vehicleBodyCondition;

        return $this;
    }

    /**
     * Get vehicleBodyCondition.
     *
     * @return string
     */
    public function getVehicleBodyCondition()
    {
        return $this->vehicleBodyCondition;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Appointment
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return Appointment
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set vehicleModel.
     *
     * @param Model $vehicleModel
     *
     * @return Appointment
     */
    public function setVehicleModel(Model $vehicleModel = null)
    {
        $this->vehicleModel = $vehicleModel;

        return $this;
    }

    /**
     * Get vehicleModel.
     *
     * @return Model
     */
    public function getVehicleModel()
    {
        return $this->vehicleModel;
    }

    /**
     * Set vehicleModelType.
     *
     * @param ModelType $vehicleModelType
     *
     * @return Appointment
     */
    public function setVehicleModelType(ModelType $vehicleModelType = null)
    {
        $this->vehicleModelType = $vehicleModelType;

        return $this;
    }

    /**
     * Get vehicleModelType.
     *
     * @return ModelType
     */
    public function getVehicleModelType()
    {
        return $this->vehicleModelType;
    }

    /**
     * Set branchTiming.
     *
     * @param Timing $branchTiming
     *
     * @return Appointment
     */
    public function setBranchTiming(Timing $branchTiming = null)
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
     * Set details.
     *
     * @param AppointmentDetails $details
     *
     * @return Appointment
     */
    public function setDetails(AppointmentDetails $details = null)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details.
     *
     * @return AppointmentDetails
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Gets statuses.
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_OFFER_ACCEPTED => 'Offer Accepted',
            self::STATUS_OFFER_REJECTED => 'Offer Rejected',
            self::STATUS_INVALID => 'Invalid',
        ];
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Appointment
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get vehicleMake.
     *
     * @return Make
     */
    public function getVehicleMake()
    {
        return $this->vehicleMake;
    }

    /**
     * Set vehicleMake.
     *
     * @param Make $vehicleMake
     *
     * @return $this
     */
    public function setVehicleMake(Make $vehicleMake)
    {
        $this->vehicleMake = $vehicleMake;

        return $this;
    }

    /**
     * Get vehicleMileage.
     *
     * @return int
     */
    public function getVehicleMileage()
    {
        return $this->vehicleMileage;
    }

    /**
     * Set vehicleMileage.
     *
     * @param $vehicleMileage
     *
     * @return $this
     */
    public function setVehicleMileage($vehicleMileage)
    {
        $this->vehicleMileage = $vehicleMileage;

        return $this;
    }

    /**
     * Set vehicleYear.
     *
     * @param int $vehicleYear
     *
     * @return Appointment
     */
    public function setVehicleYear($vehicleYear)
    {
        $this->vehicleYear = $vehicleYear;

        return $this;
    }

    /**
     * Get vehicleYear.
     *
     * @return int
     */
    public function getVehicleYear()
    {
        return $this->vehicleYear;
    }

    /**
     * Set vehicleTransmission.
     *
     * @param string $vehicleTransmission
     *
     * @return Appointment
     */
    public function setVehicleTransmission($vehicleTransmission)
    {
        $this->vehicleTransmission = $vehicleTransmission;

        return $this;
    }

    /**
     * Get vehicleTransmission.
     *
     * @return string
     */
    public function getVehicleTransmission()
    {
        return $this->vehicleTransmission;
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
     * Set branch.
     *
     * @param $branch
     *
     * @return $this
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDateBooked()
    {
        return $this->dateBooked;
    }

    /**
     * Set date.
     *
     * @param \DateTime $dateBooked
     *
     * @return $this
     */
    public function setDateBooked(\DateTime $dateBooked)
    {
        $this->dateBooked = $dateBooked;

        return $this;
    }

    /**
     * Get from.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set from.
     *
     * @param string $from
     *
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set to.
     *
     * @param $to
     *
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }
}
