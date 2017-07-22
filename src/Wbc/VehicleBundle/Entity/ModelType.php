<?php

namespace Wbc\VehicleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Entity class for vehicle model types.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="vehicle_model_type")
 * @ORM\Entity(repositoryClass="Wbc\VehicleBundle\Repository\ModelTypeRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class ModelType
{
    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="Wbc\VehicleBundle\Entity\Model", inversedBy="modelTypes")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id")
     * @Serializer\Expose()
     */
    protected $model;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="trim", type="string", length=100, nullable=true)
     * @Serializer\Expose()
     */
    private $trim;

    /**
     * @var string
     *
     * @ORM\Column(name="trim_source_id", type="string", length=100, nullable=true)
     */
    private $trimSourceId;

    /**
     * @var string
     *
     * @ORM\Column(name="engine", type="smallint", length=5)
     * @Serializer\Expose()
     */
    private $engine;

    /**
     * @var string
     *
     * @ORM\Column(name="transmission", type="string", length=100, nullable=true)
     * @Serializer\Expose()
     */
    private $transmission;

    /**
     * @var string
     *
     * @ORM\Column(name="transmission_source_id", type="string", length=100, nullable=true)
     */
    private $transmissionSourceId;

    /**
     * @var int
     *
     * @ORM\Column(name="seats", type="smallint", length=2, nullable=true)
     * @Serializer\Expose()
     */
    private $seats;

    /**
     * @var int
     *
     * @ORM\Column(name="cylinders", type="smallint", length=2, nullable=true)
     * @Serializer\Expose()
     */
    private $cylinders;

    /**
     * @var string
     *
     * @ORM\Column(name="body_type", type="string", length=100, nullable=true)
     * @Serializer\Expose()
     */
    private $bodyType;

    /**
     * @var string
     *
     * @ORM\Column(name="body_type_source_id", type="string", length=100, nullable=true)
     */
    private $bodyTypeSourceId;

    /**
     * @var array
     *
     * @ORM\Column(name="years", type="json_array", nullable=true)
     * @Serializer\Expose()
     */
    private $years;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_gcc", type="boolean", options={"default": true})
     * @Serializer\Expose()
     */
    private $isGcc;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    public function __toString()
    {
        $name = $this->getName();

        return $name ?: '';
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
     * Set seats.
     *
     * @param int $seats
     *
     * @return ModelType
     */
    public function setSeats($seats)
    {
        $this->seats = $seats;

        return $this;
    }

    /**
     * Get seats.
     *
     * @return int
     */
    public function getSeats()
    {
        return $this->seats;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return ModelType
     */
    public function setCreatedAt($createdAt)
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
     * @return ModelType
     */
    public function setUpdatedAt($updatedAt)
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
     * Set model.
     *
     * @param Model $model
     *
     * @return ModelType
     */
    public function setModel(Model $model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set trim.
     *
     * @param string $trim
     *
     * @return ModelType
     */
    public function setTrim($trim)
    {
        $this->trim = $trim;

        return $this;
    }

    /**
     * Get trim.
     *
     * @return string
     */
    public function getTrim()
    {
        return $this->trim;
    }

    /**
     * Set trimSourceId.
     *
     * @param string $trimSourceId
     *
     * @return ModelType
     */
    public function setTrimSourceId($trimSourceId)
    {
        $this->trimSourceId = $trimSourceId;

        return $this;
    }

    /**
     * Get trimSourceId.
     *
     * @return string
     */
    public function getTrimSourceId()
    {
        return $this->trimSourceId;
    }

    /**
     * Set bodyType.
     *
     * @param string $bodyType
     *
     * @return ModelType
     */
    public function setBodyType($bodyType)
    {
        $this->bodyType = $bodyType;

        return $this;
    }

    /**
     * Get bodyType.
     *
     * @return string
     */
    public function getBodyType()
    {
        return $this->bodyType;
    }

    /**
     * Set bodyTypeSourceId.
     *
     * @param string $bodyTypeSourceId
     *
     * @return ModelType
     */
    public function setBodyTypeSourceId($bodyTypeSourceId)
    {
        $this->bodyTypeSourceId = $bodyTypeSourceId;

        return $this;
    }

    /**
     * Get bodyTypeSourceId.
     *
     * @return string
     */
    public function getBodyTypeSourceId()
    {
        return $this->bodyTypeSourceId;
    }

    /**
     * Set years.
     *
     * @param array $years
     *
     * @return ModelType
     */
    public function setYears($years)
    {
        $this->years = array_unique($years);

        return $this;
    }

    /**
     * Get years.
     *
     * @return array
     */
    public function getYears()
    {
        return $this->years;
    }

    /**
     * Set isGcc.
     *
     * @param bool $isGcc
     *
     * @return ModelType
     */
    public function setIsGcc($isGcc)
    {
        $this->isGcc = $isGcc;

        return $this;
    }

    /**
     * Get isGcc.
     *
     * @return bool
     */
    public function getIsGcc()
    {
        return $this->isGcc;
    }

    /**
     * Set transmission.
     *
     * @param string $transmission
     *
     * @return ModelType
     */
    public function setTransmission($transmission)
    {
        $this->transmission = $transmission;

        return $this;
    }

    /**
     * Get transmission.
     *
     * @return string
     */
    public function getTransmission()
    {
        return $this->transmission;
    }

    /**
     * Set transmissionSourceId.
     *
     * @param string $transmissionSourceId
     *
     * @return ModelType
     */
    public function setTransmissionSourceId($transmissionSourceId)
    {
        $this->transmissionSourceId = $transmissionSourceId;

        return $this;
    }

    /**
     * Get transmissionSourceId.
     *
     * @return string
     */
    public function getTransmissionSourceId()
    {
        return $this->transmissionSourceId;
    }

    /**
     * Set cylinders.
     *
     * @param int $cylinders
     *
     * @return ModelType
     */
    public function setCylinders($cylinders)
    {
        $this->cylinders = $cylinders;

        return $this;
    }

    /**
     * Get cylinders.
     *
     * @return int
     */
    public function getCylinders()
    {
        return $this->cylinders;
    }

    /**
     * Set engine.
     *
     * @param int $engine
     *
     * @return ModelType
     */
    public function setEngine($engine)
    {
        $this->engine = $engine;

        return $this;
    }

    /**
     * Get engine.
     *
     * @return int
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Number of passengers.
     *
     * @return int
     */
    public function getPassengerNumber()
    {
        //remove the driver's seat
        return $this->seats ? $this->seats - 1 : $this->seats;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("name")
     *
     * @return string
     */
    public function getName()
    {
        return sprintf('%s - %s - %s (%sL)', $this->trim, $this->bodyType, $this->transmission, number_format($this->engine / 1000, 1));
    }

    /**
     * Returns flattened years for admin view.
     *
     * @return string
     */
    public function getFlattenedYears()
    {
        return implode(',', $this->years);
    }
}
