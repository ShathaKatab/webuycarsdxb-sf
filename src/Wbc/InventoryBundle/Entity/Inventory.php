<?php

declare(strict_types=1);

namespace Wbc\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Wbc\BranchBundle\Entity\Deal;
use Wbc\BranchBundle\Entity\Inspection;
use Wbc\UserBundle\Entity\User;
use Wbc\VehicleBundle\Entity\Make;
use Wbc\VehicleBundle\Entity\Model;
use Wbc\VehicleBundle\Entity\ModelType;

/**
 * Class Inventory.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="inventory")
 * @ORM\Entity()
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Inventory
{
    const STATUS_IN_STOCK = 'in-stock';
    const STATUS_SOLD = 'sold';
    const STATUS_IN_GARAGE = 'in-garage';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Make
     */
    protected $make;

    /**
     * @var Model
     *
     * @ORM\ManyToOne(targetEntity="Wbc\VehicleBundle\Entity\Model")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $model;

    /**
     * @var ModelType
     *
     * @ORM\ManyToOne(targetEntity="Wbc\VehicleBundle\Entity\ModelType")
     * @ORM\JoinColumn(name="model_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $modelType;

    /**
     * @var int
     *
     * @ORM\Column(name="vehicle_year", type="smallint")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $year;

    /**
     * @var string
     *
     * @ORM\Column(name="transmission", type="string", length=15, nullable=true)
     *
     * @Assert\Choice(choices={"manual", "automatic"})
     *
     * @Serializer\Expose()
     */
    protected $transmission;

    /**
     * @var int
     *
     * @ORM\Column(name="mileage", type="bigint")
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=5000, max=250000)
     *
     * @Serializer\Expose()
     */
    protected $mileage;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_specifications", type="string", length=10, nullable=true)
     *
     * @Assert\Choice(choices={"gcc", "usa", "jpn", "euro", "other"})
     *
     * @Serializer\Expose()
     */
    protected $specifications;

    /**
     * @var string
     *
     * @ORM\Column(name="body_condition", type="string", length=30, nullable=true)
     *
     * @Assert\Choice(choices={"good", "fair", "excellent"})
     *
     * @Serializer\Expose()
     */
    protected $bodyCondition;

    /**
     * @var string
     *
     * @ORM\Column(name="options", type="string", length=30, nullable=true)
     *
     * @Assert\Choice(choices={"basic", "mid", "full"})
     *
     * @Serializer\Expose()
     */
    protected $options;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=30, nullable=true)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $color;

    /**
     * @var float
     *
     * @ORM\Column(name="price_purchased", type="decimal", precision=11, scale=2)
     *
     * @Assert\NotBlank()
     */
    protected $pricePurchased;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="purchased_at", type="datetime", nullable=true)
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @Serializer\Expose()
     */
    protected $purchasedAt;

    /**
     * @var float
     *
     * @ORM\Column(name="price_sold", type="decimal", precision=11, scale=2, nullable=true)
     */
    protected $priceSold;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sold_at", type="datetime", nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $soldAt;

    /**
     * @var Dealer
     *
     * @ORM\ManyToOne(targetEntity="Wbc\InventoryBundle\Entity\Dealer")
     * @ORM\JoinColumn(name="sold_to_dealer_id", referencedColumnName="id", nullable=true)
     */
    protected $soldToDealer;

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
     * @ORM\OneToOne(targetEntity="Wbc\BranchBundle\Entity\Deal", inversedBy="inventory")
     * @ORM\JoinColumn(name="deal_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $deal;

    /**
     * @var Inspection
     *
     * @ORM\OneToOne(targetEntity="Wbc\BranchBundle\Entity\Inspection", inversedBy="inventory")
     * @ORM\JoinColumn(name="inspection_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $inspection;

    /**
     * @var UsedCar
     *
     * @ORM\OneToOne(targetEntity="Wbc\InventoryBundle\Entity\UsedCar", mappedBy="inventory")
     */
    protected $usedCar;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by_id", referencedColumnName="id", nullable=false)
     *
     * @Serializer\Expose()
     */
    protected $createdBy;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=true)
     *
     * @Assert\NotBlank()
     */
    protected $status;

    /**
     * @var string
     */
    protected $transitionName;

    /**
     * Inventory constructor.
     *
     * @param Deal $deal
     */
    public function __construct(Deal $deal = null)
    {
        $this->status = self::STATUS_IN_STOCK;
        $this->setDeal($deal);
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
     * @return Make
     */
    public function getMake()
    {
        if (!$this->make && $this->model) {
            return $this->model->getMake();
        }

        return $this->make;
    }

    /**
     * @param Make $make
     *
     * @return self
     */
    public function setMake(Make $make)
    {
        $this->make = $make;

        return $this;
    }

    /**
     * Set year.
     *
     * @param int $year
     *
     * @return Inventory
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year.
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set transmission.
     *
     * @param string $transmission
     *
     * @return Inventory
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
     * Set mileage.
     *
     * @param int $mileage
     *
     * @return Inventory
     */
    public function setMileage($mileage)
    {
        $this->mileage = $mileage;

        return $this;
    }

    /**
     * Get mileage.
     *
     * @return int
     */
    public function getMileage()
    {
        return $this->mileage;
    }

    /**
     * Set specifications.
     *
     * @param string $specifications
     *
     * @return Inventory
     */
    public function setSpecifications($specifications)
    {
        $this->specifications = $specifications;

        return $this;
    }

    /**
     * Get specifications.
     *
     * @return string
     */
    public function getSpecifications()
    {
        return $this->specifications;
    }

    /**
     * Set bodyCondition.
     *
     * @param string $bodyCondition
     *
     * @return Inventory
     */
    public function setBodyCondition($bodyCondition)
    {
        $this->bodyCondition = $bodyCondition;

        return $this;
    }

    /**
     * Get bodyCondition.
     *
     * @return string
     */
    public function getBodyCondition()
    {
        return $this->bodyCondition;
    }

    /**
     * Set options.
     *
     * @param string $options
     *
     * @return Inventory
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options.
     *
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set color.
     *
     * @param string $color
     *
     * @return Inventory
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Inventory
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
     * @return Inventory
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
     * @return Inventory
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
     * Set modelType.
     *
     * @param ModelType $modelType
     *
     * @return Inventory
     */
    public function setModelType(ModelType $modelType = null)
    {
        $this->modelType = $modelType;

        return $this;
    }

    /**
     * Get modelType.
     *
     * @return ModelType
     */
    public function getModelType()
    {
        return $this->modelType;
    }

    /**
     * Set deal.
     *
     * @param Deal $deal
     *
     * @return Inventory
     */
    public function setDeal(Deal $deal = null)
    {
        $this->deal = $deal;

        if ($deal) {
            $this->setPricePurchased($deal->getPricePurchased());
        }

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
     * Set createdBy.
     *
     * @param User $createdBy
     *
     * @return Inventory
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
     * Set pricePurchased.
     *
     * @param float $pricePurchased
     *
     * @return Inventory
     */
    public function setPricePurchased($pricePurchased)
    {
        if (!(int) $this->pricePurchased && $pricePurchased) {
            $this->pricePurchased = $pricePurchased;
        } else {
            $this->pricePurchased = 0; //hack for cannot be null error
        }

        return $this;
    }

    /**
     * Get pricePurchased.
     *
     * @return float
     */
    public function getPricePurchased()
    {
        return (float) $this->pricePurchased;
    }

    /**
     * Set priceSold.
     *
     * @param float $priceSold
     *
     * @return Inventory
     */
    public function setPriceSold($priceSold)
    {
        $this->priceSold = $priceSold;

        return $this;
    }

    /**
     * Get priceSold.
     *
     * @return float
     */
    public function getPriceSold()
    {
        return $this->priceSold;
    }

    /**
     * Set soldAt.
     *
     * @param \DateTime $soldAt
     *
     * @return Inventory
     */
    public function setSoldAt($soldAt)
    {
        $this->soldAt = $soldAt;

        return $this;
    }

    /**
     * Get soldAt.
     *
     * @return \DateTime
     */
    public function getSoldAt()
    {
        return $this->soldAt;
    }

    /**
     * Set soldToDealer.
     *
     * @param Dealer $soldToDealer
     *
     * @return Inventory
     */
    public function setSoldToDealer(Dealer $soldToDealer = null)
    {
        $this->soldToDealer = $soldToDealer;

        return $this;
    }

    /**
     * Get soldToDealer.
     *
     * @return Dealer
     */
    public function getSoldToDealer()
    {
        return $this->soldToDealer;
    }

    /**
     * Set usedCar.
     *
     * @param UsedCar $usedCar
     *
     * @return Inventory
     */
    public function setUsedCar(UsedCar $usedCar = null)
    {
        $this->usedCar = $usedCar;

        return $this;
    }

    /**
     * Get usedCar.
     *
     * @return UsedCar
     */
    public function getUsedCar()
    {
        return $this->usedCar;
    }

    /**
     * Set purchasedAt.
     *
     * @param \DateTime $purchasedAt
     *
     * @return Inventory
     */
    public function setPurchasedAt($purchasedAt)
    {
        $this->purchasedAt = $purchasedAt;

        return $this;
    }

    /**
     * Get purchasedAt.
     *
     * @return \DateTime
     */
    public function getPurchasedAt()
    {
        return $this->purchasedAt;
    }

    /**
     * @return int
     */
    public function getAge()
    {
        if (!$this->purchasedAt || !$this->soldAt) {
            return;
        }

        return $this->soldAt->diff($this->purchasedAt)->days;
    }

    /**
     * @return int
     */
    public function getAdditionalCost()
    {
        return 0; //no expenses
    }

    /**
     * @return float|int
     */
    public function getOverallCost()
    {
        return $this->pricePurchased + $this->getAdditionalCost();
    }

    /**
     * @return float
     */
    public function getGrossProfit()
    {
        return $this->priceSold - $this->pricePurchased;
    }

    /**
     * @return float
     */
    public function getNetProfit()
    {
        return $this->priceSold - $this->getOverallCost();
    }

    /**
     * @return string|User
     */
    public function getSalesman()
    {
        $deal = $this->getDeal();

        if (!$deal) {
            return '';
        }

        return $deal->getCreatedBy();
    }

    public function getSource()
    {
        $deal = $this->getDeal();

        if (!$deal) {
            return '';
        }

        return $deal->getAppointment()->getValuation()->getSource();
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Inventory
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

    public static function getStatuses()
    {
        return [
            self::STATUS_IN_STOCK => 'In Stock',
            self::STATUS_IN_GARAGE => 'In Garage',
            self::STATUS_SOLD => 'Sold',
        ];
    }

    /**
     * Set inspection.
     *
     * @param Inspection $inspection
     *
     * @return Inventory
     */
    public function setInspection(Inspection $inspection = null)
    {
        $this->inspection = $inspection;

        if ($inspection) {
            $appointment = $inspection->getAppointment();

            $this->setPricePurchased($inspection->getPriceOffered());
            $this->model = $inspection->getVehicleModel();
            $this->modelType = $inspection->getVehicleModelType();
            $this->year = $inspection->getVehicleYear();
            $this->transmission = $inspection->getVehicleTransmission();
            $this->mileage = $inspection->getVehicleMileage();
            $this->specifications = $inspection->getVehicleSpecifications();
            $this->bodyCondition = $inspection->getVehicleBodyCondition();
            $this->options = $appointment ? $appointment->getVehicleOption() : null;
            $this->color = $inspection->getVehicleColor();
        }

        return $this;
    }

    /**
     * Get inspection.
     *
     * @return \Wbc\BranchBundle\Entity\Inspection
     */
    public function getInspection()
    {
        return $this->inspection;
    }

    /**
     * @return string
     */
    public function getTransitionName(): ?string
    {
        return $this->transitionName;
    }

    /**
     * @param string $transitionName
     *
     * @return self
     */
    public function setTransitionName(string $transitionName): self
    {
        $this->transitionName = $transitionName;

        return $this;
    }
}
