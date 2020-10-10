<?php

declare(strict_types=1);

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
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default": true})
     */
    private $enabled;

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

    /**
     * @var Make
     */
    private $make;

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
    public function getId(): int
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
    public function setSeats(int $seats): self
    {
        $this->seats = $seats;

        return $this;
    }

    /**
     * Get seats.
     *
     * @return null|int
     */
    public function getSeats(): ?int
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
     * @return ModelType
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
     * Set model.
     *
     * @param null|Model $model
     *
     * @return ModelType
     */
    public function setModel(Model $model = null): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return null|Model
     */
    public function getModel(): ?Model
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
    public function setTrim(string $trim): self
    {
        $this->trim = $trim;

        return $this;
    }

    /**
     * Get trim.
     *
     * @return null|string
     */
    public function getTrim(): ?string
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
    public function setTrimSourceId(string $trimSourceId): self
    {
        $this->trimSourceId = $trimSourceId;

        return $this;
    }

    /**
     * Get trimSourceId.
     *
     * @return string
     */
    public function getTrimSourceId(): string
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
    public function setBodyType(string $bodyType): self
    {
        $this->bodyType = $bodyType;

        return $this;
    }

    /**
     * Get bodyType.
     *
     * @return null|string
     */
    public function getBodyType(): ?string
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
    public function setBodyTypeSourceId(string $bodyTypeSourceId): self
    {
        $this->bodyTypeSourceId = $bodyTypeSourceId;

        return $this;
    }

    /**
     * Get bodyTypeSourceId.
     *
     * @return string
     */
    public function getBodyTypeSourceId(): string
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
    public function setYears(array $years): self
    {
        $this->years = array_unique($years);

        return $this;
    }

    /**
     * Get years.
     *
     * @return null|array
     */
    public function getYears(): ?array
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
    public function setIsGcc(bool $isGcc): self
    {
        $this->isGcc = $isGcc;

        return $this;
    }

    /**
     * Get isGcc.
     *
     * @return null|bool
     */
    public function getIsGcc(): ?bool
    {
        return $this->isGcc;
    }

    /**
     * Set transmission.
     *
     * @param string|null $transmission
     *
     * @return ModelType
     */
    public function setTransmission(?string $transmission = null): self
    {
        $this->transmission = $transmission;

        return $this;
    }

    /**
     * Get transmission.
     *
     * @return null|string
     */
    public function getTransmission(): ?string
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
    public function setTransmissionSourceId(string $transmissionSourceId): self
    {
        $this->transmissionSourceId = $transmissionSourceId;

        return $this;
    }

    /**
     * Get transmissionSourceId.
     *
     * @return null|string
     */
    public function getTransmissionSourceId(): ?string
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
    public function setCylinders(int $cylinders): self
    {
        $this->cylinders = $cylinders;

        return $this;
    }

    /**
     * Get cylinders.
     *
     * @return null|int
     */
    public function getCylinders(): ?int
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
    public function setEngine(int $engine): self
    {
        $this->engine = $engine;

        return $this;
    }

    /**
     * Get engine.
     *
     * @return null|int
     */
    public function getEngine(): ?int
    {
        return (int) $this->engine;
    }

    /**
     * Number of passengers.
     *
     * @return int
     */
    public function getPassengerNumber(): int
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
    public function getName(): string
    {
        return sprintf('%s - %s - %s (%sL)', $this->trim, $this->bodyType, $this->transmission, number_format($this->engine / 1000, 1));
    }

    /**
     * Returns flattened years for admin view.
     *
     * @return string
     */
    public function getFlattenedYears(): string
    {
        return implode(',', $this->years);
    }

    /**
     * isEnabled.
     *
     * @return null|bool
     */
    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * setEnabled.
     *
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * getMake.
     *
     * @return null|Make
     */
    public function getMake(): ?Make
    {
        $model = $this->getModel();

        return null !== $model ? $model->getMake() : null;
    }

    /**
     * setMake.
     *
     * @param Make $make
     *
     * @return $this
     */
    public function setMake(Make $make): self
    {
        $this->make = $make;

        return $this;
    }
}
