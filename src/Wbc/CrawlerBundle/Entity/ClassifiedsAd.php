<?php

namespace Wbc\CrawlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class ClassifiedsAd.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="crawler_classifieds_ad")
 * @ORM\Entity()
 * @SuppressWarnings(PHPMD.ShortVariable, PHPMD.BooleanGetMethodName)
 */
class ClassifiedsAd
{
    const SOURCE_DUBIZZLE = 'dubizzle.com';
    const SOURCE_GETTHAT = 'getthat.com';
    const SOURCE_MANHEIM = 'manheim.com';
    const SOURCE_INSPECTION = 'inspection';
    const MILEAGE_KM = 'km';
    const MILEAGE_MILES = 'miles';
    const CURRENCY_AED = 'AED';
    const CURRENCY_USD = 'USD';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", nullable=true)
     */
    protected $url;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_make", type="string", length=100, nullable=true)
     */
    protected $make;

    /**
     * @var string
     *
     * @ORM\Column(name="vehicle_model", type="string", length=100, nullable=true)
     */
    protected $model;

    /**
     * @var ClassifiedsModel
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\CrawlerBundle\Entity\ClassifiedsModel", fetch="EAGER", inversedBy="classifiedAds")
     * @ORM\JoinColumn(name="classifieds_model_id", referencedColumnName="id", nullable=true)
     */
    protected $classifiedsModel;

    /**
     * @var ClassifiedsModelType
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\CrawlerBundle\Entity\ClassifiedsModelType", fetch="EAGER")
     * @ORM\JoinColumn(name="classifieds_model_type_id", referencedColumnName="id", nullable=true)
     */
    protected $classifiedsModelType;

    /**
     * @var string
     *
     * @ORM\Column(name="model_type", type="string", length=100, nullable=true)
     */
    protected $modelType;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    protected $year;

    /**
     * @var int
     *
     * @ORM\Column(name="cylinders", type="smallint", nullable=true)
     */
    protected $cylinders;

    /**
     * @var string
     *
     * @ORM\Column(name="exterior_color", type="string", length=100, nullable=true)
     */
    protected $exteriorColor;

    /**
     * @var string
     *
     * @ORM\Column(name="interior_color", type="string", length=100, nullable=true)
     */
    protected $interiorColor;

    /**
     * @var int
     *
     * @ORM\Column(name="mileage", type="integer", nullable=true)
     */
    protected $mileage;

    /**
     * @var int
     *
     * @ORM\Column(name="mileage_suffix", type="string", length=5, options={"default": "km"}, nullable=true)
     */
    protected $mileageSuffix;

    /**
     * @var string
     *
     * @ORM\Column(name="body_type", type="string", length=100, nullable=true)
     */
    protected $bodyType;

    /**
     * @var int
     *
     * @ORM\Column(name="doors", type="smallint", nullable=true)
     */
    protected $doors;

    /**
     * @var string
     *
     * @ORM\Column(name="specifications", type="string", length=15, nullable=true)
     */
    protected $specifications;

    /**
     * @var bool
     *
     * @ORM\Column(name="used", type="boolean", options={"default": true}, nullable=true)
     */
    protected $isUsed;


    /**
     * @var bool
     *
     * @ORM\Column(name="price_updated", type="boolean", options={"default": true})
     */
    private $updated;

    /**
     * @var string
     *
     * @ORM\Column(name="body_condition", type="string", length=100, nullable=true)
     */
    protected $bodyCondition;

    /**
     * @var string
     *
     * @ORM\Column(name="mechanical_condition", type="string", length=100, nullable=true)
     */
    protected $mechanicalCondition;

    /**
     * @var string
     *
     * @ORM\Column(name="horsepower", type="string", length=60, nullable=true)
     */
    protected $horsepower;

    /**
     * @var string
     *
     * @ORM\Column(name="engine_size", type="string", length=15, nullable=true)
     */
    protected $engineSize;

    /**
     * @var string
     *
     * @ORM\Column(name="transmission", type="string", length=15, nullable=true)
     */
    protected $transmission;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    protected $price;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=100, options={"default": "AED"}, nullable=true)
     */
    protected $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=100, nullable=true)
     */
    protected $city;

    /**
     * @var bool
     *
     * @ORM\Column(name="dealer_name", type="text", nullable=true)
     */
    protected $dealerName;

    /**
     * @var string
     *
     * @ORM\Column(name="source_id", type="text", nullable=true)
     */
    protected $sourceId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="source_created_at", type="datetime", nullable=true)
     */
    protected $sourceCreatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="source_updated_at", type="datetime", nullable=true)
     */
    protected $sourceUpdatedAt;

    /**
     * @var array
     *
     * @ORM\Column(name="image_urls", type="json_array", nullable=true)
     */
    protected $imageUrls;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=100)
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
     * ClassifiedsAd Constructor.
     *
     * @param $source
     */
    public function __construct($source)
    {
        $this->source = $source;
        $this->currency = self::CURRENCY_AED;
        $this->isUsed = true;
        $this->imageUrls = [];
        $this->mileageSuffix = self::MILEAGE_KM;
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
     * Set title.
     *
     * @param string $title
     *
     * @return ClassifiedsAd
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return ClassifiedsAd
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set year.
     *
     * @param int $year
     *
     * @return ClassifiedsAd
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
     * Set cylinders.
     *
     * @param int $cylinders
     *
     * @return ClassifiedsAd
     */
    public function setCylinders($cylinders)
    {
        $this->cylinders = intval($cylinders);

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
     * Set exteriorColor.
     *
     * @param string $exteriorColor
     *
     * @return ClassifiedsAd
     */
    public function setExteriorColor($exteriorColor)
    {
        $this->exteriorColor = $exteriorColor;

        return $this;
    }

    /**
     * Get exteriorColor.
     *
     * @return string
     */
    public function getExteriorColor()
    {
        return $this->exteriorColor;
    }

    /**
     * Set interiorColor.
     *
     * @param string $interiorColor
     *
     * @return ClassifiedsAd
     */
    public function setInteriorColor($interiorColor)
    {
        $this->interiorColor = $interiorColor;

        return $this;
    }

    /**
     * Get interiorColor.
     *
     * @return string
     */
    public function getInteriorColor()
    {
        return $this->interiorColor;
    }

    /**
     * Set mileage.
     *
     * @param int $mileage
     *
     * @return ClassifiedsAd
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
     * Set bodyType.
     *
     * @param string $bodyType
     *
     * @return ClassifiedsAd
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
     * Set doors.
     *
     * @param int $doors
     *
     * @return ClassifiedsAd
     */
    public function setDoors($doors)
    {
        $this->doors = $doors;

        return $this;
    }

    /**
     * Get doors.
     *
     * @return int
     */
    public function getDoors()
    {
        return $this->doors;
    }

    /**
     * Set specifications.
     *
     * @param string $specifications
     *
     * @return ClassifiedsAd
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
     * Set isUsed.
     *
     * @param bool $isUsed
     *
     * @return ClassifiedsAd
     */
    public function setIsUsed($isUsed)
    {
        $this->isUsed = $isUsed;

        return $this;
    }

    /**
     * Get isUsed.
     *
     * @return bool
     */
    public function isUsed()
    {
        return $this->isUsed;
    }

    /**
     * Set updated.
     *
     * @param bool $updated
     *
     * @return ClassifiedsAd
     */
    public function setUpdated(bool $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return bool|null
     */
    public function isUpdated(): ?bool
    {
        return $this->updated;
    }

    /**
     * Set bodyCondition.
     *
     * @param string $bodyCondition
     *
     * @return ClassifiedsAd
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
     * Set mechanicalCondition.
     *
     * @param string $mechanicalCondition
     *
     * @return ClassifiedsAd
     */
    public function setMechanicalCondition($mechanicalCondition)
    {
        $this->mechanicalCondition = $mechanicalCondition;

        return $this;
    }

    /**
     * Get mechanicalCondition.
     *
     * @return string
     */
    public function getMechanicalCondition()
    {
        return $this->mechanicalCondition;
    }

    /**
     * Set horsepower.
     *
     * @param string $horsepower
     *
     * @return ClassifiedsAd
     */
    public function setHorsepower($horsepower)
    {
        $this->horsepower = $horsepower;

        return $this;
    }

    /**
     * Get horsepower.
     *
     * @return string
     */
    public function getHorsepower()
    {
        return $this->horsepower;
    }

    /**
     * Set engineSize.
     *
     * @param string $engineSize
     *
     * @return ClassifiedsAd
     */
    public function setEngineSize($engineSize)
    {
        $this->engineSize = $engineSize;

        return $this;
    }

    /**
     * Get engineSize.
     *
     * @return string
     */
    public function getEngineSize()
    {
        return $this->engineSize;
    }

    /**
     * Set transmission.
     *
     * @param string $transmission
     *
     * @return ClassifiedsAd
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
     * Set price.
     *
     * @param float $price
     *
     * @return ClassifiedsAd
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set currency.
     *
     * @param string $currency
     *
     * @return ClassifiedsAd
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
     * Set city.
     *
     * @param string $city
     *
     * @return ClassifiedsAd
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set dealerName.
     *
     * @param string $dealerName
     *
     * @return ClassifiedsAd
     */
    public function setDealerName($dealerName)
    {
        $this->dealerName = $dealerName;

        return $this;
    }

    /**
     * Get dealerName.
     *
     * @return string
     */
    public function getDealerName()
    {
        return $this->dealerName;
    }

    /**
     * Set sourceId.
     *
     * @param string $sourceId
     *
     * @return ClassifiedsAd
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;

        return $this;
    }

    /**
     * Get sourceId.
     *
     * @return string
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * Set sourceCreatedAt.
     *
     * @param \DateTime $sourceCreatedAt
     *
     * @return ClassifiedsAd
     */
    public function setSourceCreatedAt(\DateTime $sourceCreatedAt = null)
    {
        $this->sourceCreatedAt = $sourceCreatedAt;

        return $this;
    }

    /**
     * Get sourceCreatedAt.
     *
     * @return \DateTime
     */
    public function getSourceCreatedAt()
    {
        return $this->sourceCreatedAt;
    }

    /**
     * Set sourceUpdatedAt.
     *
     * @param \DateTime $sourceUpdatedAt
     *
     * @return ClassifiedsAd
     */
    public function setSourceUpdatedAt(\DateTime $sourceUpdatedAt = null)
    {
        $this->sourceUpdatedAt = $sourceUpdatedAt;

        return $this;
    }

    /**
     * Get sourceUpdatedAt.
     *
     * @return \DateTime
     */
    public function getSourceUpdatedAt()
    {
        return $this->sourceUpdatedAt;
    }

    /**
     * @param $imageUrl
     *
     * @return ClassifiedsAd
     */
    public function addImageUrl($imageUrl)
    {
        $this->imageUrls[] = $imageUrl;

        return $this;
    }

    /**
     * Set imageUrls.
     *
     * @param array $imageUrls
     *
     * @return ClassifiedsAd
     */
    public function setImageUrls(array $imageUrls)
    {
        $this->imageUrls = $imageUrls;

        return $this;
    }

    /**
     * Get imageUrls.
     *
     * @return array
     */
    public function getImageUrls()
    {
        return $this->imageUrls;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return ClassifiedsAd
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
     * @return ClassifiedsAd
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
     * Set classifiedsModel.
     *
     * @param ClassifiedsModel $classifiedsModel
     *
     * @return ClassifiedsAd
     */
    public function setClassifiedsModel(ClassifiedsModel $classifiedsModel = null)
    {
        $this->classifiedsModel = $classifiedsModel;

        return $this;
    }

    /**
     * Get classifiedsModel.
     *
     * @return ClassifiedsModel
     */
    public function getClassifiedsModel()
    {
        return $this->classifiedsModel;
    }

    /**
     * Set make.
     *
     * @param string $make
     *
     * @return ClassifiedsAd
     */
    public function setMake($make)
    {
        $this->make = $make;

        return $this;
    }

    /**
     * Get make.
     *
     * @return string
     */
    public function getMake()
    {
        return $this->make;
    }

    /**
     * Set model.
     *
     * @param string $model
     *
     * @return ClassifiedsAd
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get isUsed.
     *
     * @return bool
     */
    public function getIsUsed()
    {
        return $this->isUsed;
    }

    /**
     * Set source.
     *
     * @param string $source
     *
     * @return ClassifiedsAd
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
     * Set mileageSuffix.
     *
     * @param string $mileageSuffix
     *
     * @return ClassifiedsAd
     */
    public function setMileageSuffix($mileageSuffix)
    {
        $this->mileageSuffix = $mileageSuffix;

        return $this;
    }

    /**
     * Get mileageSuffix.
     *
     * @return string
     */
    public function getMileageSuffix()
    {
        return $this->mileageSuffix;
    }

    /**
     * Set classifiedsModelType.
     *
     * @param ClassifiedsModelType $classifiedsModelType
     *
     * @return ClassifiedsAd
     */
    public function setClassifiedsModelType(ClassifiedsModelType $classifiedsModelType = null)
    {
        $this->classifiedsModelType = $classifiedsModelType;

        return $this;
    }

    /**
     * Get classifiedsModelType.
     *
     * @return \Wbc\CrawlerBundle\Entity\ClassifiedsModelType
     */
    public function getClassifiedsModelType()
    {
        return $this->classifiedsModelType;
    }

    /**
     * Set modelType.
     *
     * @param string $modelType
     *
     * @return ClassifiedsAd
     */
    public function setModelType($modelType)
    {
        $this->modelType = $modelType;

        return $this;
    }

    /**
     * Get modelType.
     *
     * @return string
     */
    public function getModelType()
    {
        return $this->modelType;
    }
}
