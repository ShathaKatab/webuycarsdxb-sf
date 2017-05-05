<?php

namespace Wbc\BranchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Branch.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="branch")
 * @ORM\Entity(repositoryClass="Wbc\BranchBundle\Repository\BranchRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Branch
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
     * @ORM\Column(name="name", type="string", length=100, unique=true)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=128, unique=true)
     *
     * @Gedmo\Slug(separator="-", fields={"name"})
     *
     * @Serializer\Expose()
     */
    protected $slug;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", options={"default": false})
     */
    protected $active;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="city_slug", type="string", length=100)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $citySlug;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=15)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $phoneNumber;

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
     * @var
     *
     * @ORM\OneToMany(targetEntity="\Wbc\BranchBundle\Entity\Timing", mappedBy="branch")
     */
    protected $timings;

    /**
     * Branch Constructor.
     */
    public function __construct()
    {
        $this->active = false;
        $this->timings = new ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return Branch
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
     * Set slug.
     *
     * @param string $slug
     *
     * @return Branch
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return Branch
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
     * Set latitude.
     *
     * @param float $latitude
     *
     * @return Branch
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude.
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude.
     *
     * @param float $longitude
     *
     * @return Branch
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude.
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return Branch
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set citySlug.
     *
     * @param string $citySlug
     *
     * @return Branch
     */
    public function setCitySlug($citySlug)
    {
        $this->citySlug = $citySlug;

        return $this;
    }

    /**
     * Get citySlug.
     *
     * @return string
     */
    public function getCitySlug()
    {
        return $this->citySlug;
    }

    /**
     * Set phoneNumber.
     *
     * @param string $phoneNumber
     *
     * @return Branch
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber.
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Branch
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
     * @return Branch
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
     * Add timing.
     *
     * @param Timing $timing
     *
     * @return Branch
     */
    public function addTiming(Timing $timing)
    {
        $this->timings[] = $timing;

        return $this;
    }

    /**
     * Remove timing.
     *
     * @param Timing $timing
     */
    public function removeTiming(Timing $timing)
    {
        $this->timings->removeElement($timing);
    }

    /**
     * Get timings.
     *
     * @return ArrayCollection
     */
    public function getTimings()
    {
        return $this->timings;
    }

    public function __toString()
    {
        $name = $this->getName();

        if ($name) {
            return $this->getName();
        }

        return '';
    }
}
