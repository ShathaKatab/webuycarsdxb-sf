<?php

namespace Wbc\BranchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Wbc\UserBundle\Entity\User;
use Wbc\ValuationBundle\Entity\Valuation;
use Wbc\VehicleBundle\Entity\Make;
use Wbc\VehicleBundle\Entity\Model;
use Wbc\VehicleBundle\Entity\ModelType;

/**
 * Class Appointment.
 *
 * @ORM\Table(name="appointment")
 * @ORM\Entity(repositoryClass="Wbc\BranchBundle\Repository\AppointmentRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class Appointment
{
    const STATUS_NEW = 'new';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_INVALID_CONTACT = 'invalid_contact';
    const STATUS_CALLBACK = 'callback';
    const STATUS_CHECKED_IN = 'checked-in';
    const STATUS_DUPLICATE = 'duplicate';

    /**
     * @var int
     *
     * @ORM\Column(type="guid")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
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
     * @var Model
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\VehicleBundle\Entity\Model")
     * @ORM\JoinColumn(name="vehicle_model_id", referencedColumnName="id")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $vehicleModel;

    /**
     * @var ModelType
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\VehicleBundle\Entity\ModelType")
     * @ORM\JoinColumn(name="vehicle_model_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     *
     * @Serializer\Expose()
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
     * @ORM\Column(name="vehicle_transmission", type="string", length=15, nullable=true)
     *
     * @Assert\Choice(choices={"manual", "automatic"})
     *
     * @Serializer\Expose()
     */
    protected $vehicleTransmission;

    /**
     * @var int
     *
     * @ORM\Column(name="vehicle_mileage", type="bigint")
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=5000, max=250000)
     *
     * @Serializer\Expose()
     */
    protected $vehicleMileage;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_specifications", type="string", length=10, nullable=true)
     *
     * @Assert\Choice(choices={"gcc", "usa", "jpn", "euro", "other"})
     *
     * @Serializer\Expose()
     */
    protected $vehicleSpecifications;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_body_condition", type="string", length=30, nullable=true)
     *
     * @Assert\Choice(choices={"good", "fair", "excellent"})
     *
     * @Serializer\Expose()
     */
    protected $vehicleBodyCondition;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_color", type="string", length=30, nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\Choice(choices={"white", "silver", "black", "grey", "blue", "red", "brown", "green"})
     *
     * @Serializer\Expose()
     */
    protected $vehicleColor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_booked", type="date")
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    protected $dateBooked;

    /**
     * @var Timing
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\BranchBundle\Entity\Timing")
     * @ORM\JoinColumn(name="branch_timing", referencedColumnName="id", onDelete="SET NULL", nullable=true)})
     *
     * @Serializer\Expose()
     */
    protected $branchTiming;

    /**
     * @var AppointmentDetails
     *
     * @ORM\OneToOne(targetEntity="\Wbc\BranchBundle\Entity\AppointmentDetails", mappedBy="appointment", cascade={"persist"})
     *
     * @Serializer\Expose()
     */
    protected $details;

    /**
     * @var Valuation
     *
     * @ORM\OneToOne(targetEntity="\Wbc\ValuationBundle\Entity\Valuation", inversedBy="appointment")
     * @ORM\JoinColumn(name="valuation_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $valuation;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20)
     *
     * @Assert\NotBlank()
     */
    protected $status;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by_id", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

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
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $notes;

    /**
     * @var Branch
     */
    protected $branch;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $to;

    /**
     * @var Make
     */
    protected $vehicleMake;

    /**
     * Appointment Constructor.
     *
     * @param Valuation $valuation
     */
    public function __construct(Valuation $valuation = null)
    {
        $this->status = self::STATUS_NEW;
        $this->setValuation($valuation);
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
            self::STATUS_NEW => 'New',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_INVALID_CONTACT => 'Invalid Contact',
            self::STATUS_CALLBACK => 'Call Back',
            self::STATUS_CHECKED_IN => 'Checked In',
            self::STATUS_DUPLICATE => 'Duplicate',
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

    /**
     * Set vehicleColor.
     *
     * @param string $vehicleColor
     *
     * @return Appointment
     */
    public function setVehicleColor($vehicleColor)
    {
        $this->vehicleColor = $vehicleColor;

        return $this;
    }

    /**
     * Get vehicleColor.
     *
     * @return string
     */
    public function getVehicleColor()
    {
        return $this->vehicleColor;
    }

    /**
     * Set valuation.
     *
     * @param Valuation $valuation
     *
     * @return Appointment
     */
    public function setValuation(Valuation $valuation = null)
    {
        $this->valuation = $valuation;

        if ($valuation) {
            $this->vehicleModel = $valuation->getVehicleModel();
            $this->vehicleYear = $valuation->getVehicleYear();
            $this->vehicleModelType = $valuation->getVehicleModelType();
            $this->vehicleMileage = $valuation->getVehicleMileage();
            $this->vehicleColor = $valuation->getVehicleColor();
            $this->vehicleBodyCondition = $valuation->getVehicleBodyCondition();
            $this->name = $valuation->getName();
            $this->emailAddress = $valuation->getEmailAddress();
            $this->mobileNumber = $valuation->getMobileNumber();
        }

        return $this;
    }

    /**
     * Get valuation.
     *
     * @return Valuation
     */
    public function getValuation()
    {
        return $this->valuation;
    }

    /**
     * Set createdBy.
     *
     * @param User $createdBy
     *
     * @return Appointment
     */
    public function setCreatedBy(User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set Vehicle Make.
     *
     * @param Make $make
     *
     * @return $this
     */
    public function setVehicleMake(Make $make)
    {
        $this->vehicleMake = $make;

        return $this;
    }

    /**
     * Get Vehicle Make.
     *
     * @return Make
     */
    public function getVehicleMake()
    {
        return $this->vehicleMake;
    }

    /**
     * Set notes.
     *
     * @param string $notes
     *
     * @return Appointment
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes.
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Gets SmsTimingString.
     *
     * @return string
     */
    public function getSmsTimingString()
    {
        return sprintf('%s @ "%s"', $this->dateBooked->format('d/m/y'), $this->branchTiming->getTimingString());
    }

    /**
     * Gets online price.
     *
     * @return float
     */
    public function getPriceOnline()
    {
        if ($this->valuation) {
            return $this->valuation->getPriceOnline();
        }
    }

    /**
     * Gets day booked.
     *
     * @return int
     */
    public function getDayBooked()
    {
        return $this->branchTiming->getDayBooked();
    }
}
