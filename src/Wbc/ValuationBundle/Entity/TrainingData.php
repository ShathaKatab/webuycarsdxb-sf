<?php

declare(strict_types=1);

namespace Wbc\ValuationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Wbc\CrawlerBundle\Entity\ClassifiedsAd;
use Wbc\InventoryBundle\Entity\Inventory;
use Wbc\VehicleBundle\Entity\Make;
use Wbc\VehicleBundle\Entity\Model;

/**
 * Class TrainingData.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="valuation_training_data")
 * @ORM\Entity()
 */
class TrainingData
{
    /**
     * @var array
     */
    public static $colors = [
        'other' => 0,
        'white' => 1,
        'silver' => 2,
        'black' => 3,
        'grey' => 4,
        'blue' => 5,
        'red' => 6,
        'brown' => 7,
        'green' => 8,
    ];

    /**
     * @var array
     */
    public static $bodyConditions = [
        'other' => 0,
        'fair' => 1,
        'good' => 2,
        'excellent' => 3,
    ];
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
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\VehicleBundle\Entity\Make")
     * @ORM\JoinColumn(name="make_id", referencedColumnName="id", nullable=false)
     */
    protected $make;

    /**
     * @var Model
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\VehicleBundle\Entity\Model")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id", nullable=false)
     */
    protected $model;

    /**
     * @var ClassifiedsAd
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\CrawlerBundle\Entity\ClassifiedsAd")
     * @ORM\JoinColumn(name="crawler_classifieds_ad_id", referencedColumnName="id", nullable=true)
     */
    protected $crawlerClassifiedsAd;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer")
     */
    protected $year;

    /**
     * @var int
     *
     * @ORM\Column(name="mileage", type="integer")
     */
    protected $mileage;

    /**
     * @var int
     *
     * @ORM\Column(name="color", type="smallint")
     */
    protected $color;

    /**
     * @var int
     *
     * @ORM\Column(name="body_condition", type="smallint")
     */
    protected $bodyCondition;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    protected $price;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=100)
     */
    protected $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=60)
     */
    protected $source;

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
     * @var Inventory
     *
     * @ORM\ManyToOne(targetEntity="Wbc\InventoryBundle\Entity\Inventory")
     * @ORM\JoinColumn(name="inventory_id", referencedColumnName="id", nullable=true)
     */
    protected $inventory;

    /**
     * @var array
     */
    protected $colorsMapping = [
        'yellow' => 'other',
        'gold' => 'other',
        'burgundy' => 'other',
        'tan' => 'other',
        'orange' => 'other',
        'beige' => 'brown',
        'purple' => 'other',
        'teal' => 'blue',
        'other color' => 'other',
    ];

    /**
     * @var array
     */
    protected $bodyConditionsMapping = [
        'perfect inside out' => 'excellent',
        'a bit of wear and tear, all repaired' => 'good',
        'no accidents' => 'excellent',
        'a bit of wear and tear, a few issues' => 'fair',
        'lots of wear and tear to the body' => 'fair',
        "i don't know" => 'other',
        'perfect inside and out' => 'excellent',
        'no accidents, very few faults' => 'good',
        'a bit of wear & tear, all repaired' => 'good',
        'normal wear & tear, a few issues' => 'fair',
        'lots of wear & tear to the body' => 'fair',
    ];

    /**
     * TrainingData Constructor.
     *
     * @param Make   $make
     * @param Model  $model
     * @param int    $year
     * @param int    $mileage
     * @param string $color
     * @param string $bodyCondition
     * @param mixed  $price
     * @param string $source
     */
    public function __construct(Make $make, Model $model, $year, $mileage, $color, $bodyCondition, $price, $source)
    {
        $color = strtolower($color);
        $bodyCondition = strtolower($bodyCondition);

        $this->make = $make;
        $this->model = $model;
        $this->year = (int) $year;
        $this->mileage = (int) $mileage;
        $this->price = round($price);
        $this->source = $source;

        if (isset(self::$colors[$color])) {
            $this->color = self::$colors[$color];
        } else {
            if (isset($this->colorsMapping[$color])) {
                $this->color = self::$colors[$this->colorsMapping[$color]];
            } else {
                $this->color = self::$colors['other'];
            }
        }

        if (isset(self::$bodyConditions[$bodyCondition])) {
            $this->bodyCondition = self::$bodyConditions[$bodyCondition];
        } else {
            if (isset($this->bodyConditionsMapping[$bodyCondition])) {
                $this->bodyCondition = self::$bodyConditions[$this->bodyConditionsMapping[$bodyCondition]];
            } else {
                $this->bodyCondition = self::$bodyConditions['other'];
            }
        }

        //max mileage => 999,999
        if ($this->mileage > 999999) {
            $this->mileage = 999999;
        }

        //max price => 19,999,999
        if ($this->price > 19999999) {
            $this->price = 19999999;
        }
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
     * Set year.
     *
     * @param int $year
     *
     * @return TrainingData
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
     * Set mileage.
     *
     * @param int $mileage
     *
     * @return TrainingData
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
     * Set color.
     *
     * @param int $color
     *
     * @return TrainingData
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return int
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set bodyCondition.
     *
     * @param int $bodyCondition
     *
     * @return TrainingData
     */
    public function setBodyCondition($bodyCondition)
    {
        $this->bodyCondition = $bodyCondition;

        return $this;
    }

    /**
     * Get bodyCondition.
     *
     * @return int
     */
    public function getBodyCondition()
    {
        return $this->bodyCondition;
    }

    /**
     * Set make.
     *
     * @param Make $make
     *
     * @return TrainingData
     */
    public function setMake(Make $make = null)
    {
        $this->make = $make;

        return $this;
    }

    /**
     * Get make.
     *
     * @return Make
     */
    public function getMake()
    {
        return $this->make;
    }

    /**
     * Set model.
     *
     * @param Model $model
     *
     * @return TrainingData
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
     * Set price.
     *
     * @param int $price
     *
     * @return TrainingData
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set source.
     *
     * @param string $source
     *
     * @return TrainingData
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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return TrainingData
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
     * @return TrainingData
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
     * Set crawlerClassifiedsAd.
     *
     * @param ClassifiedsAd $crawlerClassifiedsAd
     *
     * @return TrainingData
     */
    public function setCrawlerClassifiedsAd(ClassifiedsAd $crawlerClassifiedsAd = null)
    {
        $this->crawlerClassifiedsAd = $crawlerClassifiedsAd;

        return $this;
    }

    /**
     * Get crawlerClassifiedsAd.
     *
     * @return ClassifiedsAd
     */
    public function getCrawlerClassifiedsAd()
    {
        return $this->crawlerClassifiedsAd;
    }

    /**
     * Set currency.
     *
     * @param string $currency
     *
     * @return TrainingData
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set inventory.
     *
     * @param Inventory $inventory
     *
     * @return TrainingData
     */
    public function setInventory(Inventory $inventory = null)
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * Get inventory.
     *
     * @return Inventory
     */
    public function getInventory()
    {
        return $this->inventory;
    }
}
