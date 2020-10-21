<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Wbc\InventoryBundle\Entity\Inventory;
use Wbc\UserBundle\Entity\User;
use Wbc\VehicleBundle\Entity\Make;
use Wbc\VehicleBundle\Entity\Model;
use Wbc\VehicleBundle\Entity\ModelType;

/**
 * Class Inspection.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="inspection")
 * @ORM\Entity()
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Inspection
{
    const STATUS_NEW            = 'new';
    const STATUS_INVALID        = 'invalid';
    const STATUS_OFFER_ACCEPTED = 'offer_accepted';
    const STATUS_OFFER_REJECTED = 'offer_rejected';
    const STATUS_PENDING        = 'pending';

    /**
     * @var string
     */
    public $bookedAtTiming;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Expose()
     */
    protected $id;

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
     * @Serializer\Expose()
     */
    protected $vehicleColor;

    /**
     * @var Appointment
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\BranchBundle\Entity\Appointment", inversedBy="inspections")
     * @ORM\JoinColumn(name="appointment_id", referencedColumnName="id")
     */
    protected $appointment;

    /**
     * @var float
     *
     * @ORM\Column(name="price_offered", type="decimal", precision=11, scale=2, nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $priceOffered;

    /**
     * @var float
     *
     * @ORM\Column(name="price_expected", type="decimal", precision=11, scale=2, nullable=true)
     */
    protected $priceExpected;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\UserBundle\Entity\User", fetch="EAGER")
     * @ORM\JoinColumn(name="created_by_id", referencedColumnName="id", nullable=false)
     */
    protected $createdBy;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20)
     *
     * @Assert\NotBlank()
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
     * @var Deal
     *
     * @ORM\OneToOne(targetEntity="Wbc\BranchBundle\Entity\Deal", mappedBy="inspection")
     */
    protected $deal;

    /**
     * @var Inventory
     *
     * @ORM\OneToOne(targetEntity="Wbc\InventoryBundle\Entity\Inventory", mappedBy="inspection", fetch="EAGER")
     */
    protected $inventory;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $notes;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=20, nullable=true)
     */
    protected $source;

    /**
     * Inspection Constructor.
     *
     * @param Appointment $appointment
     */
    public function __construct(Appointment $appointment = null)
    {
        $this->setAppointment($appointment);
        $this->status = self::STATUS_NEW;
    }

    /**
     * Get id.
     *
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set vehicleYear.
     *
     * @param int $vehicleYear
     *
     * @return Inspection
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
     * @return Inspection
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
     * Set vehicleMileage.
     *
     * @param int $vehicleMileage
     *
     * @return Inspection
     */
    public function setVehicleMileage(int $vehicleMileage): self
    {
        $this->vehicleMileage = $vehicleMileage;

        return $this;
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
     * Set vehicleSpecifications.
     *
     * @param string $vehicleSpecifications
     *
     * @return Inspection
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
     * @return Inspection
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
     * Set vehicleColor.
     *
     * @param string $vehicleColor
     *
     * @return Inspection
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
     * Set priceOffered.
     *
     * @param string $priceOffered
     *
     * @return Inspection
     */
    public function setPriceOffered(string $priceOffered): self
    {
        $this->priceOffered = $priceOffered;

        return $this;
    }

    /**
     * Get priceOffered.
     *
     * @return null|float
     */
    public function getPriceOffered(): ?float
    {
        return (float) $this->priceOffered;
    }

    /**
     * Set priceExpected.
     *
     * @param string $priceExpected
     *
     * @return Inspection
     */
    public function setPriceExpected(string $priceExpected): self
    {
        $this->priceExpected = $priceExpected;

        return $this;
    }

    /**
     * Get priceExpected.
     *
     * @return null|float
     */
    public function getPriceExpected(): ?float
    {
        return (float) $this->priceExpected;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Inspection
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
     * @return Inspection
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
     * @return Inspection
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
     * Gets vehicle make.
     *
     * @return null|Make
     */
    public function getVehicleMake(): ?Make
    {
        if ($this->vehicleModel) {
            return $this->vehicleModel->getMake();
        }

        return null;
    }

    /**
     * Set vehicleModelType.
     *
     * @param null|ModelType $vehicleModelType
     *
     * @return Inspection
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
     * Set appointment.
     *
     * @param null|Appointment $appointment
     *
     * @return Inspection
     */
    public function setAppointment(Appointment $appointment = null): self
    {
        $this->appointment = $appointment;

        if ($appointment) {
            $this->vehicleModel          = $appointment->getVehicleModel();
            $this->vehicleMileage        = $appointment->getVehicleMileage();
            $this->vehicleModelType      = $appointment->getVehicleModelType();
            $this->vehicleBodyCondition  = $appointment->getVehicleBodyCondition();
            $this->vehicleColor          = $appointment->getVehicleColor();
            $this->vehicleYear           = $appointment->getVehicleYear();
            $this->vehicleTransmission   = $appointment->getVehicleTransmission();
            $this->vehicleSpecifications = $appointment->getVehicleSpecifications();
            $this->source                = $appointment->getSource();
        }

        return $this;
    }

    /**
     * Get appointment.
     *
     * @return null|Appointment
     */
    public function getAppointment(): ?Appointment
    {
        return $this->appointment;
    }

    /**
     * Set createdBy.
     *
     * @param User $createdBy
     *
     * @return Inspection
     */
    public function setCreatedBy(User $createdBy): self
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
     * Set deal.
     *
     * @param null|Deal $deal
     *
     * @return Inspection
     */
    public function setDeal(Deal $deal = null): self
    {
        $this->deal = $deal;

        return $this;
    }

    /**
     * Get deal.
     *
     * @return null|Deal
     */
    public function getDeal(): ?Deal
    {
        return $this->deal;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Inspection
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
     * Gets online valuation price.
     *
     * @return null|float
     */
    public function getPriceOnline(): ?float
    {
        if ($this->appointment) {
            $valuation = $this->appointment->getValuation();

            if ($valuation) {
                return $valuation->getPriceOnline();
            }
        }

        return null;
    }

    /**
     * Gets timing string.
     *
     * @return null|string
     */
    public function getTimingString(): ?string
    {
        if ($this->appointment) {
            $timing = $this->appointment->getBranchTiming();

            if ($timing) {
                return $timing->getTimingString();
            }
        }

        return null;
    }

    /**
     * Gets statuses.
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW            => 'New',
            self::STATUS_OFFER_ACCEPTED => 'Offer Accepted',
            self::STATUS_OFFER_REJECTED => 'Offer Rejected',
            self::STATUS_PENDING        => 'Pending',
            self::STATUS_INVALID        => 'Invalid',
        ];
    }

    /**
     * Set notes.
     *
     * @param string $notes
     *
     * @return Inspection
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
     * Set source.
     *
     * @param string $source
     *
     * @return Inspection
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
     * Set inventory.
     *
     * @param null|Inventory $inventory
     *
     * @return Inspection
     */
    public function setInventory(Inventory $inventory = null): self
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * Get inventory.
     *
     * @return null|Inventory
     */
    public function getInventory(): ?Inventory
    {
        return $this->inventory;
    }

    /**
     * dayBooked.
     *
     * @return null|string
     */
    public function dayBooked(): ?string
    {
        $dayBooked   = null;
        $appointment = $this->getAppointment();

        if (null !== $appointment) {
            $dayBooked = $appointment->dayBooked();
        }

        return $dayBooked;
    }

    /**
     * bookedAtTiming.
     *
     * @return null|string
     */
    public function bookedAtTiming(): ?string
    {
        $timing      = null;
        $appointment = $this->getAppointment();

        if (null !== $appointment) {
            $timing = $appointment->bookedAtTiming();
        }

        return $timing;
    }
}
