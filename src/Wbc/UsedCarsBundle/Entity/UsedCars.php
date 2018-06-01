<?php

declare(strict_types=1);

namespace Wbc\UsedCarsBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Gallery;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Wbc\BranchBundle\Entity\Deal;
use Wbc\UserBundle\Entity\User;
use Wbc\VehicleBundle\Entity\Make;
use Wbc\VehicleBundle\Entity\Model;
use Wbc\VehicleBundle\Entity\ModelType;

/**
 * Class UsedCars.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="used_cars")
 * @ORM\Entity(repositoryClass="Wbc\UsedCarsBundle\Repository\UsedCarsRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class UsedCars
{
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
     * @ORM\Column(name="mechanical_condition", type="string", length=30, nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $mechanicalCondition;

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
     * @var int
     *
     * @ORM\Column(name="doors", type="smallint", nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $doors;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=30, nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\Choice(choices={"white", "silver", "black", "grey", "blue", "red", "brown", "green", "other"})
     *
     * @Serializer\Expose()
     */
    protected $color;

    /**
     * @var string
     *
     * @ORM\Column(name="body_type", type="string", length=30, nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $bodyType;

    /**
     * @var int
     *
     * @ORM\Column(name="cylinders", type="smallint", nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $cylinders;

    /**
     * @var int
     *
     * @ORM\Column(name="horsepower", type="smallint", nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $horsepower;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="decimal", precision=11, scale=2)
     */
    protected $price;

    /**
     * @var string
     *
     * @ORM\Column(name="description_text", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var Gallery
     *
     * @ORM\OneToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Gallery", cascade={"persist"})
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
     */
    protected $gallery;

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
     * @ORM\Column(name="guid", type="guid", unique=true)
     */
    protected $guid;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", options={"default": false})
     */
    protected $active;

    /**
     * @var Deal
     *
     * @ORM\OneToOne(targetEntity="Wbc\BranchBundle\Entity\Deal", inversedBy="usedCar")
     * @ORM\JoinColumn(name="deal_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $deal;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by_id", referencedColumnName="id", nullable=false)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $createdBy;

    /**
     * UsedCars constructor.
     *
     * @param Deal $deal
     */
    public function __construct(Deal $deal = null)
    {
        $this->active = true;
        $this->guid = self::generateGuid();
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
     * @return UsedCars
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
     * @return UsedCars
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
     * @return UsedCars
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
     * @return UsedCars
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
     * @return UsedCars
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
     * @return UsedCars
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
     * Set options.
     *
     * @param string $options
     *
     * @return UsedCars
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
     * Set doors.
     *
     * @param int $doors
     *
     * @return UsedCars
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
     * Set color.
     *
     * @param string $color
     *
     * @return UsedCars
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
     * Set bodyType.
     *
     * @param string $bodyType
     *
     * @return UsedCars
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
     * Set cylinders.
     *
     * @param int $cylinders
     *
     * @return UsedCars
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
     * Set horsepower.
     *
     * @param int $horsepower
     *
     * @return UsedCars
     */
    public function setHorsepower($horsepower)
    {
        $this->horsepower = $horsepower;

        return $this;
    }

    /**
     * Get horsepower.
     *
     * @return int
     */
    public function getHorsepower()
    {
        return $this->horsepower;
    }

    /**
     * Set price.
     *
     * @param string $price
     *
     * @return UsedCars
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return UsedCars
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return UsedCars
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
     * @return UsedCars
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
     * @return UsedCars
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
     * @return UsedCars
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
     * Set gallery.
     *
     * @param Gallery $gallery
     *
     * @return UsedCars
     */
    public function setGallery(Gallery $gallery = null)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get gallery.
     *
     * @return Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    public function hasImages()
    {
        return $this->gallery && count($this->gallery->getGalleryHasMedias());
    }

    public function getTitle()
    {
        return sprintf('%d %s %s', $this->year, $this->getMake()->getName(), $this->model);
    }

    public function getFirstImage()
    {
        if (!$this->gallery) {
            return;
        }

        $galleryHasMedias = $this->gallery->getGalleryHasMedias();

        if (!$galleryHasMedias) {
            return;
        }

        return $galleryHasMedias[0]->getMedia();
    }

    public function getImages()
    {
        $images = [];

        if (!$this->gallery) {
            return $images;
        }

        $galleryHasMedias = $this->gallery->getGalleryHasMedias();

        foreach ($galleryHasMedias as $galleryHasMedia) {
            $images[] = $galleryHasMedia->getMedia();
        }

        return $images;
    }

    /**
     * Set guid.
     *
     * @param string $guid
     *
     * @return UsedCars
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;

        return $this;
    }

    /**
     * Get guid.
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    public static function generateGuid()
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return UsedCars
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set deal.
     *
     * @param Deal $deal
     *
     * @return UsedCars
     */
    public function setDeal(Deal $deal = null)
    {
        $this->deal = $deal;

        if ($deal) {
            $appointment = $deal->getAppointment();

            if ($appointment) {
                $this->model = $appointment->getVehicleModel();
                $this->modelType = $appointment->getVehicleModelType();
                $this->year = $appointment->getVehicleYear();
                $this->transmission = $appointment->getVehicleTransmission();
                $this->mileage = $appointment->getVehicleMileage();
                $this->specifications = $appointment->getVehicleSpecifications();
                $this->bodyCondition = $appointment->getVehicleBodyCondition();
                $this->options = $appointment->getVehicleOption();
                $this->color = $appointment->getVehicleColor();
                $this->price = $appointment->getValuation()->getPriceOnline();
            }
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
     * Set createdBy
     *
     * @param \Wbc\UserBundle\Entity\User $createdBy
     * @return UsedCars
     */
    public function setCreatedBy(\Wbc\UserBundle\Entity\User $createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Wbc\UserBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
