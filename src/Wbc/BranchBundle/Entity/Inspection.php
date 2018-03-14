<?php

namespace Wbc\BranchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
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
    const STATUS_NEW = 'new';
    const STATUS_INVALID = 'invalid';
    const STATUS_OFFER_ACCEPTED = 'offer_accepted';
    const STATUS_OFFER_REJECTED = 'offer_rejected';

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
     * @Assert\NotBlank()
     * @Assert\Choice(choices={"white", "silver", "black", "grey", "blue", "red", "brown", "green", "other"})
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
     * @ORM\ManyToOne(targetEntity="\Wbc\UserBundle\Entity\User")
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
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $notes;

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
     * @return int
     */
    public function getId()
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
     * @return Inspection
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
     * Set vehicleMileage.
     *
     * @param int $vehicleMileage
     *
     * @return Inspection
     */
    public function setVehicleMileage($vehicleMileage)
    {
        $this->vehicleMileage = $vehicleMileage;

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
     * Set vehicleSpecifications.
     *
     * @param string $vehicleSpecifications
     *
     * @return Inspection
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
     * @return Inspection
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
     * Set vehicleColor.
     *
     * @param string $vehicleColor
     *
     * @return Inspection
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
     * Set priceOffered.
     *
     * @param string $priceOffered
     *
     * @return Inspection
     */
    public function setPriceOffered($priceOffered)
    {
        $this->priceOffered = $priceOffered;

        return $this;
    }

    /**
     * Get priceOffered.
     *
     * @return string
     */
    public function getPriceOffered()
    {
        return $this->priceOffered;
    }

    /**
     * Set priceExpected.
     *
     * @param string $priceExpected
     *
     * @return Inspection
     */
    public function setPriceExpected($priceExpected)
    {
        $this->priceExpected = $priceExpected;

        return $this;
    }

    /**
     * Get priceExpected.
     *
     * @return string
     */
    public function getPriceExpected()
    {
        return $this->priceExpected;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Inspection
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
     * @return Inspection
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
     * @return Inspection
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
     * Gets vehicle make.
     *
     * @return Make
     */
    public function getVehicleMake()
    {
        if ($this->vehicleModel) {
            return $this->vehicleModel->getMake();
        }
    }

    /**
     * Set vehicleModelType.
     *
     * @param ModelType $vehicleModelType
     *
     * @return Inspection
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
     * Set appointment.
     *
     * @param Appointment $appointment
     *
     * @return Inspection
     */
    public function setAppointment(Appointment $appointment = null)
    {
        $this->appointment = $appointment;

        if ($appointment) {
            $this->vehicleModel = $appointment->getVehicleModel();
            $this->vehicleMileage = $appointment->getVehicleMileage();
            $this->vehicleModelType = $appointment->getVehicleModelType();
            $this->vehicleBodyCondition = $appointment->getVehicleBodyCondition();
            $this->vehicleColor = $appointment->getVehicleColor();
            $this->vehicleYear = $appointment->getVehicleYear();
            $this->vehicleTransmission = $appointment->getVehicleTransmission();
            $this->vehicleSpecifications = $appointment->getVehicleSpecifications();
        }

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
     * Set createdBy.
     *
     * @param User $createdBy
     *
     * @return Inspection
     */
    public function setCreatedBy(User $createdBy)
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
     * Set deal.
     *
     * @param Deal $deal
     *
     * @return Inspection
     */
    public function setDeal(Deal $deal = null)
    {
        $this->deal = $deal;

        return $this;
    }

    /**
     * Get deal.
     *
     * @return Deal
     */
    public function getDeal()
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
     * Gets online valuation price.
     *
     * @return float
     */
    public function getPriceOnline()
    {
        if ($this->appointment) {
            $valuation = $this->appointment->getValuation();

            if ($valuation) {
                return $valuation->getPriceOnline();
            }
        }
    }

    /**
     * Gets timing string.
     *
     * @return string
     */
    public function getTimingString()
    {
        if ($this->appointment) {
            $timing = $this->appointment->getBranchTiming();

            if ($timing) {
                return $timing->getTimingString();
            }
        }
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
            self::STATUS_OFFER_ACCEPTED => 'Offer Accepted',
            self::STATUS_OFFER_REJECTED => 'Offer Rejected',
            self::STATUS_INVALID => 'Invalid',
        ];
    }

    /**
     * Set notes.
     *
     * @param string $notes
     *
     * @return Inspection
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
}
