<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
    const STATUS_NEW             = 'new';
    const STATUS_CONFIRMED       = 'confirmed';
    const STATUS_CANCELLED       = 'cancelled';
    const STATUS_INVALID_CONTACT = 'invalid_contact';
    const STATUS_CALLBACK        = 'callback';
    const STATUS_CHECKED_IN      = 'checked-in';
    const STATUS_DUPLICATE       = 'duplicate';
    const STATUS_INSPECTED       = 'inspected';

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
     * @ORM\Column(name="email_address", type="string", length=100, nullable=true)
     *
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
     * @Assert\Choice(choices={"white", "silver", "black", "grey", "blue", "red", "brown", "green", "other"})
     *
     * @Serializer\Expose()
     */
    protected $vehicleColor;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_option", type="string", length=30, nullable=true)
     *
     * @Assert\NotBlank(groups={"frontend"})
     * @Assert\Choice(choices={"base", "mid", "full"})
     *
     * @Serializer\Expose()
     */
    protected $vehicleOption;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="booked_at", type="datetime")
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    protected $bookedAt;

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
     * @var Inspection
     *
     * @ORM\OneToMany(targetEntity="\Wbc\BranchBundle\Entity\Inspection", mappedBy="appointment", fetch="EAGER")
     */
    protected $inspections;

    /**
     * @var bool
     *
     * @ORM\Column(name="sms_sent", type="boolean", nullable=true, options={"default": false})
     */
    protected $smsSent;

    /**
     * @var AppointmentReminder
     *
     * @ORM\OneToOne(targetEntity="\Wbc\BranchBundle\Entity\AppointmentReminder", mappedBy="appointment", cascade={"remove"})
     *
     * @Serializer\Expose()
     */
    protected $appointmentReminder;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=20, nullable=true)
     */
    protected $source;

    /**
     * @var Branch
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\BranchBundle\Entity\Branch")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    public $branch;

    /**
     * @var Make
     */
    protected $vehicleMake;

    /**
     * Appointment Constructor.
     *
     * @param null|Valuation $valuation
     */
    public function __construct(Valuation $valuation = null)
    {
        $this->status = self::STATUS_NEW;
        $this->setValuation($valuation);
        $this->inspections = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return null|string
     */
    public function getId(): ?string
    {
        return (string) $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Appointment
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return null|string
     */
    public function getName(): ?string
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
    public function setMobileNumber(string $mobileNumber): self
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    /**
     * Get mobileNumber.
     *
     * @return null|string
     */
    public function getMobileNumber(): ?string
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
    public function setEmailAddress(string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get emailAddress.
     *
     * @return null|string
     */
    public function getEmailAddress(): ?string
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
    public function setVehicleSpecifications(string $vehicleSpecifications): self
    {
        $this->vehicleSpecifications = $vehicleSpecifications;

        return $this;
    }

    /**
     * Get vehicleSpecifications.
     *
     * @return null|string
     */
    public function getVehicleSpecifications(): ?string
    {
        return $this->vehicleSpecifications;
    }

    /**
     * Set vehicleBodyCondition.
     *
     * @param string $vehicleBodyCondition
     *
     * @return null|Appointment
     */
    public function setVehicleBodyCondition(string $vehicleBodyCondition): self
    {
        $this->vehicleBodyCondition = $vehicleBodyCondition;

        return $this;
    }

    /**
     * Get vehicleBodyCondition.
     *
     * @return null|string
     */
    public function getVehicleBodyCondition(): ?string
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
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
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
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set vehicleModel.
     *
     * @param null|Model $vehicleModel
     *
     * @return Appointment
     */
    public function setVehicleModel(Model $vehicleModel = null): self
    {
        $this->vehicleModel = $vehicleModel;

        return $this;
    }

    /**
     * Get vehicleModel.
     *
     * @return null|Model
     */
    public function getVehicleModel(): ?Model
    {
        return $this->vehicleModel;
    }

    /**
     * Set vehicleModelType.
     *
     * @param null|ModelType $vehicleModelType
     *
     * @return Appointment
     */
    public function setVehicleModelType(ModelType $vehicleModelType = null): self
    {
        $this->vehicleModelType = $vehicleModelType;

        return $this;
    }

    /**
     * Get vehicleModelType.
     *
     * @return null|ModelType
     */
    public function getVehicleModelType(): ?ModelType
    {
        return $this->vehicleModelType;
    }

    /**
     * Set details.
     *
     * @param null|AppointmentDetails $details
     *
     * @return Appointment
     */
    public function setDetails(AppointmentDetails $details = null): self
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details.
     *
     * @return null|AppointmentDetails
     */
    public function getDetails(): ?AppointmentDetails
    {
        return $this->details;
    }

    /**
     * Gets statuses.
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW             => 'New',
            self::STATUS_CONFIRMED       => 'Confirmed',
            self::STATUS_CANCELLED       => 'Cancelled',
            self::STATUS_INVALID_CONTACT => 'Invalid Contact',
            self::STATUS_CALLBACK        => 'Call Back',
            self::STATUS_CHECKED_IN      => 'Checked In',
            self::STATUS_DUPLICATE       => 'Duplicate',
            self::STATUS_INSPECTED       => 'Inspected',
        ];
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Appointment
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return null|string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Get vehicleMileage.
     *
     * @return null|int
     */
    public function getVehicleMileage(): ?int
    {
        return (int) $this->vehicleMileage;
    }

    /**
     * Set vehicleMileage.
     *
     * @param $vehicleMileage
     *
     * @return $this
     */
    public function setVehicleMileage($vehicleMileage): self
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
    public function setVehicleYear(int $vehicleYear): self
    {
        $this->vehicleYear = $vehicleYear;

        return $this;
    }

    /**
     * Get vehicleYear.
     *
     * @return null|int
     */
    public function getVehicleYear(): ?int
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
    public function setVehicleTransmission(string $vehicleTransmission): self
    {
        $this->vehicleTransmission = $vehicleTransmission;

        return $this;
    }

    /**
     * Get vehicleTransmission.
     *
     * @return null|string
     */
    public function getVehicleTransmission(): ?string
    {
        return $this->vehicleTransmission;
    }

    /**
     * Get branch.
     *
     * @return null|Branch
     */
    public function getBranch(): ?Branch
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
    public function setVehicleColor(string $vehicleColor): self
    {
        $this->vehicleColor = $vehicleColor;

        return $this;
    }

    /**
     * Get vehicleColor.
     *
     * @return null|string
     */
    public function getVehicleColor(): ?string
    {
        return $this->vehicleColor;
    }

    /**
     * Set valuation.
     *
     * @param null|Valuation $valuation
     *
     * @return Appointment
     */
    public function setValuation(Valuation $valuation = null): self
    {
        $this->valuation = $valuation;

        if ($valuation) {
            $this->vehicleModel         = $valuation->getVehicleModel();
            $this->vehicleYear          = $valuation->getVehicleYear();
            $this->vehicleModelType     = $valuation->getVehicleModelType();
            $this->vehicleMileage       = $valuation->getVehicleMileage();
            $this->vehicleColor         = $valuation->getVehicleColor();
            $this->vehicleBodyCondition = $valuation->getVehicleBodyCondition();
            $this->vehicleOption        = $valuation->getVehicleOption();
            $this->name                 = $valuation->getName();
            $this->emailAddress         = $valuation->getEmailAddress();
            $this->mobileNumber         = $valuation->getMobileNumber();
            $this->source               = $valuation->getSource();
        }

        return $this;
    }

    /**
     * Get valuation.
     *
     * @return null|Valuation
     */
    public function getValuation(): ?Valuation
    {
        return $this->valuation;
    }

    /**
     * Set createdBy.
     *
     * @param null|User $createdBy
     *
     * @return Appointment
     */
    public function setCreatedBy(User $createdBy = null): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return null|User
     */
    public function getCreatedBy(): ?User
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
    public function setVehicleMake(Make $make): self
    {
        $this->vehicleMake = $make;

        return $this;
    }

    /**
     * Get Vehicle Make.
     *
     * @return null|Make
     */
    public function getVehicleMake(): ?Make
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
    public function setNotes(string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes.
     *
     * @return null|string
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * Gets SmsTimingString.
     *
     * @return null|string
     */
    public function getSmsTimingString(): ?string
    {
        if (null !== $this->bookedAt) {
            return sprintf('%s @ "%s"', $this->bookedAt->format('d/m/y'), $this->bookedAt->format('h:i a'));
        }

        return null;
    }

    /**
     * Gets online price.
     *
     * @return null|float
     */
    public function getPriceOnline(): ?float
    {
        if ($this->valuation) {
            return $this->valuation->getPriceOnline();
        }

        return null;
    }

    /**
     * Add inspection.
     *
     * @param Inspection $inspection
     *
     * @return Appointment
     */
    public function addInspection(Inspection $inspection): self
    {
        $this->inspections[] = $inspection;

        return $this;
    }

    /**
     * Remove inspection.
     *
     * @param Inspection $inspection
     */
    public function removeInspection(Inspection $inspection): void
    {
        $this->inspections->removeElement($inspection);
    }

    /**
     * Get inspections.
     *
     * @return ArrayCollection
     */
    public function getInspections(): ?ArrayCollection
    {
        return $this->inspections;
    }

    /**
     * Checks whether there are inspections.
     *
     * @return bool
     */
    public function hasInspections(): bool
    {
        return (bool) ($this->inspections->count());
    }

    /**
     * Gets first inspection.
     *
     * @return null|Inspection
     */
    public function getInspection(): ?Inspection
    {
        $inspections = $this->inspections;

        return $inspections instanceof ArrayCollection ? $this->inspections->first() : null;
    }

    /**
     * Set smsSent.
     *
     * @param bool $smsSent
     *
     * @return null|Appointment
     */
    public function setSmsSent(bool $smsSent): ?self
    {
        $this->smsSent = $smsSent;

        return $this;
    }

    /**
     * Get smsSent.
     *
     * @return null|bool
     */
    public function isSmsSent(): ?bool
    {
        return $this->smsSent;
    }

    /**
     * Set vehicleOption.
     *
     * @param string $vehicleOption
     *
     * @return null|Appointment
     */
    public function setVehicleOption(string $vehicleOption): ?self
    {
        $this->vehicleOption = $vehicleOption;

        return $this;
    }

    /**
     * Get vehicleOption.
     *
     * @return null|string
     */
    public function getVehicleOption(): ?string
    {
        return $this->vehicleOption;
    }

    /**
     * Get smsSent.
     *
     * @return null|bool
     */
    public function getSmsSent(): ?bool
    {
        return $this->smsSent;
    }

    /**
     * Set appointmentReminder.
     *
     * @param null|AppointmentReminder $appointmentReminder
     *
     * @return Appointment
     */
    public function setAppointmentReminder(AppointmentReminder $appointmentReminder = null): self
    {
        $this->appointmentReminder = $appointmentReminder;

        return $this;
    }

    /**
     * Get appointmentReminder.
     *
     * @return null|AppointmentReminder
     */
    public function getAppointmentReminder(): ?AppointmentReminder
    {
        return $this->appointmentReminder;
    }

    /**
     * Set source.
     *
     * @param string $source
     *
     * @return Appointment
     */
    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source.
     *
     * @return null|string
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * getBookedAt.
     *
     * @return null|\DateTime
     */
    public function getBookedAt(): ?\DateTime
    {
        return $this->bookedAt;
    }

    /**
     * setBookedAt.
     *
     * @param null|\DateTime $bookedAt
     *
     * @return $this
     */
    public function setBookedAt(?\DateTime $bookedAt = null): self
    {
        $this->bookedAt = $bookedAt;

        return $this;
    }

    /**
     * bookedAtTiming.
     *
     * @return null|string
     */
    public function bookedAtTiming(): ?string
    {
        $timing   = null;
        $bookedAt = $this->bookedAt;

        if ($bookedAt instanceof \DateTime) {
            $bookedAtTo = (clone $bookedAt)->add(new \DateInterval('PT30M'));
            $timing     = sprintf('%s - %s', $bookedAt->format('h:i A'), $bookedAtTo->format('h:i A'));
        }

        return $timing;
    }

    /**
     * dayBooked.
     *
     * @return null|string
     */
    public function dayBooked(): ?string
    {
        $dayBooked = null;
        $bookedAt  = $this->bookedAt;

        if ($bookedAt instanceof \DateTime) {
            $dayBooked = $bookedAt->format('l');
        }

        return $dayBooked;
    }
}
