<?php

namespace Wbc\BranchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Wbc\UserBundle\Entity\User;

/**
 * Class Deal.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="deal")
 * @ORM\Entity()
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Deal
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Expose()
     */
    protected $id;

    /**
     * @var Inspection
     *
     * @ORM\OneToOne(targetEntity="Wbc\BranchBundle\Entity\Inspection", inversedBy="deal")
     * @ORM\JoinColumn(name="inspection_id", referencedColumnName="id", nullable=false)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $inspection;

    /**
     * @var float
     *
     * @ORM\Column(name="price_purchased", type="decimal", precision=11, scale=2, nullable=true)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $pricePurchased;

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
     * Deal Constructor.
     *
     * @param Inspection $inspection
     */
    public function __construct(Inspection $inspection = null)
    {
        $this->inspection = $inspection;
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
     * Set pricePurchased.
     *
     * @param string $pricePurchased
     *
     * @return Deal
     */
    public function setPricePurchased($pricePurchased)
    {
        $this->pricePurchased = $pricePurchased;

        return $this;
    }

    /**
     * Get pricePurchased.
     *
     * @return string
     */
    public function getPricePurchased()
    {
        return $this->pricePurchased;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Deal
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
     * Set inspection.
     *
     * @param Inspection $inspection
     *
     * @return Deal
     */
    public function setInspection(Inspection $inspection)
    {
        $this->inspection = $inspection;

        return $this;
    }

    /**
     * Get inspection.
     *
     * @return Inspection
     */
    public function getInspection()
    {
        return $this->inspection;
    }

    /**
     * Set createdBy.
     *
     * @param User $createdBy
     *
     * @return Deal
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
}
