<?php

declare(strict_types=1);

namespace Wbc\InventoryBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Gallery;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Wbc\UserBundle\Entity\User;

/**
 * Class UsedCar.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="used_cars")
 * @ORM\Entity()
 *
 * @Serializer\ExclusionPolicy("all")
 */
class UsedCar
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
     * @var string
     *
     * @ORM\Column(name="mechanical_condition", type="string", length=30, nullable=true)
     *
     * @Serializer\Expose()
     */
    protected $mechanicalCondition;

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
     * @var Inventory
     *
     * @ORM\OneToOne(targetEntity="Wbc\InventoryBundle\Entity\Inventory", inversedBy="usedCar")
     * @ORM\JoinColumn(name="inventory_id", referencedColumnName="id", nullable=false)
     */
    protected $inventory;

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
     * UsedCar constructor.
     *
     * @param Inventory $inventory
     */
    public function __construct(Inventory $inventory = null)
    {
        $this->active = true;
        $this->guid = self::generateGuid();
        $this->setInventory($inventory);
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
     * Set mechanicalCondition.
     *
     * @param string $mechanicalCondition
     *
     * @return UsedCar
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
     * Set doors.
     *
     * @param int $doors
     *
     * @return UsedCar
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
     * Set bodyType.
     *
     * @param string $bodyType
     *
     * @return UsedCar
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
     * @return UsedCar
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
     * @return UsedCar
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
     * @return UsedCar
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
     * @return UsedCar
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
     * @return UsedCar
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
     * @return UsedCar
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
     * Set gallery.
     *
     * @param Gallery $gallery
     *
     * @return UsedCar
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
        $inventory = $this->getInventory();

        return sprintf('%d %s %s', $inventory->getYear(), $inventory->getMake()->getName(), $inventory->getModel());
    }

    public function getFirstImage()
    {
        if (!$this->gallery) {
            return;
        }

        $galleryHasMedias = $this->gallery->getGalleryHasMedias();

        if (!count($galleryHasMedias)) {
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
     * @return UsedCar
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
     * @return UsedCar
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
     * Set createdBy.
     *
     * @param User $createdBy
     *
     * @return UsedCar
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
     * Set inventory.
     *
     * @param Inventory $inventory
     *
     * @return UsedCar
     */
    public function setInventory(Inventory $inventory)
    {
        $this->inventory = $inventory;

        if ($inventory) {
            $deal = $inventory->getDeal();

            if ($deal) {
                $this->price = $deal->getPricePurchased();
            }
        }

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
