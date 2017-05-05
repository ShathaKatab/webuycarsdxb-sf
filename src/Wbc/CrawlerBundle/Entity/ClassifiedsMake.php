<?php

namespace Wbc\CrawlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations
use JMS\Serializer\Annotation as ApiSerializer;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class BaseMake.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="crawler_classifieds_make")
 * @ORM\Entity()
 * @ApiSerializer\ExclusionPolicy("all")
 * @SuppressWarnings(PHPMD.ShortVariable, PHPMD.BooleanGetMethodName)
 */
class ClassifiedsMake
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ApiSerializer\Expose
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     * @ApiSerializer\Expose
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=255, nullable=true)
     */
    protected $source;

    /**
     * @var string
     *
     * @ORM\Column(name="source_id", type="string", length=100, nullable=true)
     */
    protected $sourceId;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\Wbc\CrawlerBundle\Entity\ClassifiedsModel", mappedBy="make")
     */
    protected $models;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->models = new ArrayCollection();
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
     * @return ClassifiedsMake
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
     * Set source.
     *
     * @param string $source
     *
     * @return ClassifiedsMake
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
     * Set sourceId.
     *
     * @param string $sourceId
     *
     * @return ClassifiedsMake
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
     * @return ClassifiedsMake
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
     * @return ClassifiedsMake
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
     * Add models.
     *
     * @param \Wbc\CrawlerBundle\Entity\ClassifiedsModel $models
     *
     * @return ClassifiedsMake
     */
    public function addModel(ClassifiedsModel $models)
    {
        $this->models[] = $models;

        return $this;
    }

    /**
     * Remove models.
     *
     * @param ClassifiedsModel $models
     */
    public function removeModel(ClassifiedsModel $models)
    {
        $this->models->removeElement($models);
    }

    /**
     * Get models.
     *
     * @return ArrayCollection
     */
    public function getModels()
    {
        return $this->models;
    }
}
