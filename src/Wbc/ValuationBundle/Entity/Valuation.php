<?php

declare(strict_types=1);

namespace Wbc\ValuationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Wbc\BranchBundle\Entity\Appointment;
use Wbc\VehicleBundle\Entity\Make;
use Wbc\VehicleBundle\Entity\Model;
use Wbc\VehicleBundle\Entity\ModelType;

/**
 * Class Valuation.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="valuation")
 * @ORM\Entity(repositoryClass="Wbc\ValuationBundle\Repository\ValuationRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Valuation
{
    const STATUS_NEW = 'new';
    const STATUS_CALLBACK = 'callback';
    const STATUS_INVALID_CONTACT = 'invalid_contact';
    const STATUS_DUPLICATE = 'duplicate';
    const STATUS_RESEARCH = 'research';
    const STATUS_CANCELLED = 'cancelled';

    const SOURCE_WEBSITE_ORGANIC = 'website-organic';
    const SOURCE_CALL = 'call';
    const SOURCE_WALK_IN = 'walk-in';
    const SOURCE_OTHERS = 'others';

    /**
     * @var string
     *
     * @ORM\Column(type="guid")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @var Model
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\VehicleBundle\Entity\Model")
     * @ORM\JoinColumn(name="vehicle_model_id", referencedColumnName="id")
     *
     * @Assert\NotBlank(groups={"valuation-step-2"})
     *
     * @Serializer\Expose()
     */
    protected $vehicleModel;

    /**
     * @var int
     *
     * @ORM\Column(name="vehicle_year", type="smallint")
     *
     * @Assert\NotBlank(groups={"valuation-step-2"})
     * @Assert\Range(min=1928)
     *
     * @Serializer\Expose()
     */
    protected $vehicleYear;

    /**
     * @var ModelType
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\VehicleBundle\Entity\ModelType")
     * @ORM\JoinColumn(name="vehicle_model_type_id", referencedColumnName="id", nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $vehicleModelType;

    /**
     * @var int
     *
     * @ORM\Column(name="vehicle_mileage", type="bigint")
     *
     * @Assert\NotBlank(groups={"valuation-step-2"})
     *
     * @Serializer\Expose()
     */
    protected $vehicleMileage;

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
     * @ORM\Column(name="vehicle_body_condition", type="string", length=30, nullable=true)
     *
     * @Assert\NotBlank(groups={"valuation-step-2"})
     * @Assert\Choice(choices={"good", "fair", "excellent"})
     *
     * @Serializer\Expose()
     */
    protected $vehicleBodyCondition;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_option", type="string", length=30, nullable=true)
     *
     * @Assert\NotBlank(groups={"valuation-step-2"})
     * @Assert\Choice(choices={"base", "mid", "full"})
     *
     * @Serializer\Expose()
     */
    protected $vehicleOption;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     *
     * @Assert\NotBlank(groups={"valuation-step-3"})
     *
     * @Serializer\Expose()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email_address", type="string", length=100, nullable=true)
     *
     * @Assert\Email()
     */
    protected $emailAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_number", type="string", length=15, nullable=true)
     *
     * @Assert\NotBlank(groups={"valuation-step-3"})
     */
    protected $mobileNumber;

    /**
     * @var Appointment
     *
     * @ORM\OneToOne(targetEntity="\Wbc\BranchBundle\Entity\Appointment", mappedBy="valuation", cascade={"persist"})
     */
    protected $appointment;

    /**
     * @var float
     *
     * @ORM\Column(name="price_online", type="decimal", precision=11, scale=2, nullable=true)
     */
    protected $priceOnline;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=true)
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    protected $notes;

    /**
     * @var string
     *
     * @ORM\Column(name="reason_cancellation", type="text", nullable=true)
     */
    protected $reasonCancellation;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=20, nullable=true)
     */
    protected $source;

    /**
     * @var Make
     */
    protected $vehicleMake;

    /**
     * @var string
     */
    protected $priceImageEncoded;

    /**
     * Valuation Constructor.
     *
     * @param Appointment $appointment
     */
    public function __construct(Appointment $appointment = null)
    {
        if ($this->vehicleModel) {
            $this->vehicleMake = $this->vehicleModel->getMake();
        }

        $this->setAppointment($appointment);
        $this->source = self::SOURCE_OTHERS;
    }

    public function __toString()
    {
        return $this->getId() ? (string) $this->getId() : '';
    }

    /**
     * Get id.
     *
     * @return guid
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
     * @return Valuation
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
     * Set vehicleMileage.
     *
     * @param int $vehicleMileage
     *
     * @return Valuation
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
     * Set vehicleColor.
     *
     * @param string $vehicleColor
     *
     * @return Valuation
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
     * Set vehicleBodyCondition.
     *
     * @param string $vehicleBodyCondition
     *
     * @return Valuation
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
     * Set name.
     *
     * @param string $name
     *
     * @return Valuation
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
     * Set emailAddress.
     *
     * @param string $emailAddress
     *
     * @return Valuation
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
     * Set mobileNumber.
     *
     * @param string $mobileNumber
     *
     * @return Valuation
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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Valuation
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
     * @return Valuation
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
     * @return Valuation
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
     * @return Valuation
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
     * @return Valuation
     */
    public function setAppointment(Appointment $appointment = null)
    {
        $this->appointment = $appointment;

        if ($appointment) {
            $this->vehicleModel = $appointment->getVehicleModel();
            $this->vehicleYear = $appointment->getVehicleYear();
            $this->vehicleModelType = $appointment->getVehicleModelType();
            $this->vehicleMileage = $appointment->getVehicleMileage();
            $this->vehicleColor = $appointment->getVehicleColor();
            $this->vehicleBodyCondition = $appointment->getVehicleBodyCondition();
            $this->name = $appointment->getName();
            $this->emailAddress = $appointment->getEmailAddress();
            $this->mobileNumber = $appointment->getMobileNumber();

            $this->appointment->setValuation($this);
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
     * Set priceOnline.
     *
     * @param float $priceOnline
     *
     * @return Valuation
     */
    public function setPriceOnline($priceOnline)
    {
        $this->priceOnline = (float) $priceOnline;

        return $this;
    }

    /**
     * Get priceOnline.
     *
     * @return float
     */
    public function getPriceOnline()
    {
        return (float) ($this->priceOnline);
    }

    /**
     * @return Make
     */
    public function getVehicleMake()
    {
        if (!$this->vehicleMake && $this->vehicleModel) {
            $this->vehicleMake = $this->vehicleModel->getMake();
        }

        return $this->vehicleMake;
    }

    /**
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
     * Has Appointment.
     *
     * @return bool
     */
    public function hasAppointment()
    {
        if (null !== $this->appointment) {
            return true;
        }

        return false;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Valuation
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
     * Set notes.
     *
     * @param string $notes
     *
     * @return Valuation
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
     * Set reasonCancellation.
     *
     * @param string $reasonCancellation
     *
     * @return Valuation
     */
    public function setReasonCancellation($reasonCancellation)
    {
        $this->reasonCancellation = $reasonCancellation;

        return $this;
    }

    /**
     * Get reasonCancellation.
     *
     * @return string
     */
    public function getReasonCancellation()
    {
        return $this->reasonCancellation;
    }

    /**
     * Set source.
     *
     * @param string $source
     *
     * @return Valuation
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
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
            self::STATUS_CALLBACK => 'Call Back',
            self::STATUS_INVALID_CONTACT => 'Invalid Contact',
            self::STATUS_DUPLICATE => 'Duplicate',
            self::STATUS_RESEARCH => 'Research',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * @return string
     */
    public function getModelTypeName()
    {
        if ($this->vehicleModelType) {
            return $this->vehicleModelType->getName();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getPriceImageEncoded()
    {
        return $this->priceImageEncoded;
    }

    /**
     * @param string $priceImageEncoded
     *
     * @return Valuation
     */
    public function setPriceImageEncoded($priceImageEncoded)
    {
        $this->priceImageEncoded = $priceImageEncoded;

        return $this;
    }

    /**
     * Set vehicleOption.
     *
     * @param string $vehicleOption
     *
     * @return Valuation
     */
    public function setVehicleOption($vehicleOption)
    {
        $this->vehicleOption = $vehicleOption;

        return $this;
    }

    /**
     * Get vehicleOption.
     *
     * @return string
     */
    public function getVehicleOption()
    {
        return $this->vehicleOption;
    }
}
