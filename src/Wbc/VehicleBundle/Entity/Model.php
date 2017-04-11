<?php

namespace Wbc\VehicleBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Entity class for vehicle models.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="vehicle_model")
 * @ORM\Entity(repositoryClass="Wbc\VehicleBundle\Repository\ModelRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Model
{
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
     * @ORM\Column(name="name", type="string", length=100)
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", options={"default": true})
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="source_id", type="string", length=100)
     */
    private $sourceId;

    /**
     * @var Make
     *
     * @ORM\ManyToOne(targetEntity="Wbc\VehicleBundle\Entity\Make", inversedBy="models", fetch="EAGER")
     * @ORM\JoinColumn(name="make_id", referencedColumnName="id")
     * @Serializer\Expose()
     */
    protected $make;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Wbc\VehicleBundle\Entity\ModelType", mappedBy="model")
     * @ORM\OrderBy({"engine" = "ASC", "bodyType" = "ASC"})
     */
    protected $modelTypes;

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
     * Constructor.
     */
    public function __construct()
    {
        $this->modelTypes = new ArrayCollection();
        $this->active = true;
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
     * @return Model
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
     * Set isActive.
     *
     * @param bool $active
     *
     * @return Model
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set sourceId.
     *
     * @param string $sourceId
     *
     * @return Model
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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Model
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
     * @return Model
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
     * Set make.
     *
     * @param Make $make
     *
     * @return Model
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
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Add modelType.
     *
     * @param ModelType $modelType
     *
     * @return Model
     */
    public function addModelType(ModelType $modelType)
    {
        $this->modelTypes[] = $modelType;

        return $this;
    }

    /**
     * Remove modelType.
     *
     * @param ModelType $modelType
     */
    public function removeModelType(ModelType $modelType)
    {
        $this->modelTypes->removeElement($modelType);
    }

    /**
     * Get modelTypes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getModelTypes()
    {
        return $this->modelTypes;
    }
}
