<?php

declare(strict_types=1);

namespace Wbc\ValuationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Wbc\UserBundle\Entity\User;
use Wbc\VehicleBundle\Entity\Make;
use Wbc\VehicleBundle\Entity\Model;
use Wbc\VehicleBundle\Entity\ModelType;

/**
 * Class ValuationConfiguration.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="valuation_configuration")
 * @ORM\Entity(repositoryClass="Wbc\ValuationBundle\Repository\ValuationConfigurationRepository")
 */
class ValuationConfiguration
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Make
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\VehicleBundle\Entity\Make")
     * @ORM\JoinColumn(name="vehicle_make_id", referencedColumnName="id", nullable=true)
     */
    protected $vehicleMake;

    /**
     * @var Model
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\VehicleBundle\Entity\Model")
     * @ORM\JoinColumn(name="vehicle_model_id", referencedColumnName="id", nullable=true)
     */
    protected $vehicleModel;

    /**
     * @var ModelType
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\VehicleBundle\Entity\ModelType")
     * @ORM\JoinColumn(name="vehicle_model_type_id", referencedColumnName="id", nullable=true)
     */
    protected $vehicleModelType;

    /**
     * @var int
     *
     * @ORM\Column(name="vehicle_year", type="smallint", nullable=true)
     */
    protected $vehicleYear;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_color", type="string", length=30, nullable=true)
     */
    protected $vehicleColor;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_body_condition", type="string", length=30, nullable=true)
     */
    protected $vehicleBodyCondition;

    /**
     * @var string
     *
     * @ORM\Column(name="discount", type="decimal", precision=11, scale=2)
     */
    protected $discount;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", options={"default": false})
     */
    protected $active;

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
     * ValuationConfiguration constructor.
     */
    public function __construct()
    {
        $this->active = false;
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
     * Set vehicleYear.
     *
     * @param int $vehicleYear
     *
     * @return ValuationConfiguration
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
     * Set discount.
     *
     * @param string $discount
     *
     * @return ValuationConfiguration
     */
    public function setDiscount(string $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount.
     *
     * @return string|null
     */
    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return ValuationConfiguration
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
     * @return ValuationConfiguration
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
     * Set vehicleMake.
     *
     * @param \Wbc\VehicleBundle\Entity\Make $vehicleMake
     *
     * @return ValuationConfiguration
     */
    public function setVehicleMake(Make $vehicleMake = null): self
    {
        $this->vehicleMake = $vehicleMake;

        return $this;
    }

    /**
     * Get vehicleMake.
     *
     * @return \Wbc\VehicleBundle\Entity\Make|null
     */
    public function getVehicleMake(): ?Make
    {
        return $this->vehicleMake;
    }

    /**
     * Set vehicleModel.
     *
     * @param \Wbc\VehicleBundle\Entity\Model $vehicleModel
     *
     * @return ValuationConfiguration
     */
    public function setVehicleModel(Model $vehicleModel = null): self
    {
        $this->vehicleModel = $vehicleModel;

        return $this;
    }

    /**
     * Get vehicleModel.
     *
     * @return \Wbc\VehicleBundle\Entity\Model|null
     */
    public function getVehicleModel(): ?Model
    {
        return $this->vehicleModel;
    }

    /**
     * Set vehicleColor.
     *
     * @param string $vehicleColor
     *
     * @return ValuationConfiguration
     */
    public function setVehicleColor(string $vehicleColor): self
    {
        $this->vehicleColor = $vehicleColor;

        return $this;
    }

    /**
     * Get vehicleColor.
     *
     * @return string|null
     */
    public function getVehicleColor(): ?string
    {
        return $this->vehicleColor;
    }

    /**
     * Set vehicleBodyCondition.
     *
     * @param string $vehicleBodyCondition
     *
     * @return ValuationConfiguration
     */
    public function setVehicleBodyCondition(string $vehicleBodyCondition): self
    {
        $this->vehicleBodyCondition = $vehicleBodyCondition;

        return $this;
    }

    /**
     * Get vehicleBodyCondition.
     *
     * @return string|null
     */
    public function getVehicleBodyCondition(): ?string
    {
        return $this->vehicleBodyCondition;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return ValuationConfiguration
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * isActive.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Set createdBy.
     *
     * @param null|User $createdBy
     *
     * @return ValuationConfiguration
     */
    public function setCreatedBy(User $createdBy = null): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return User
     */
    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    /**
     * getVehicleModelType.
     *
     * @return null|ModelType
     */
    public function getVehicleModelType(): ?ModelType
    {
        return $this->vehicleModelType;
    }

    /**
     * setVehicleModelType.
     *
     * @param ModelType|null $vehicleModelType
     *
     * @return $this
     */
    public function setVehicleModelType(ModelType $vehicleModelType = null): self
    {
        $this->vehicleModelType = $vehicleModelType;

        return $this;
    }
}
