<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Holiday.
 *
 * @ORM\Table(name="branch_holiday", uniqueConstraints={@ORM\UniqueConstraint(name="wbc_holiday_unique_idx", columns={"branch_id",
 * "from_date", "to_date"})})
 * @ORM\Entity()
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class Holiday
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Expose
     */
    protected $id;

    /**
     * @var Branch
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\BranchBundle\Entity\Branch", inversedBy="timings")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose
     */
    protected $branch;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="from_date", type="date")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose
     * @Serializer\Type(
     *   "DateTime<'Y-m-d'>"
     * )
     */
    protected $from;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="to_date", type="date")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose
     * @Serializer\Type(
     *   "DateTime<'Y-m-d'>"
     * )
     */
    protected $to;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * Get id.
     *
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * getBranch.
     *
     * @return null|Branch
     */
    public function getBranch(): ?Branch
    {
        return $this->branch;
    }

    /**
     * setBranch.
     *
     * @param Branch $branch
     *
     * @return $this
     */
    public function setBranch(Branch $branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * getFrom.
     *
     * @return null|\DateTime
     */
    public function getFrom(): ?\DateTime
    {
        return $this->from;
    }

    /**
     * setFrom.
     *
     * @param \DateTime $from
     *
     * @return $this
     */
    public function setFrom(\DateTime $from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getTo(): ?\DateTime
    {
        return $this->to;
    }

    /**
     * setTo.
     *
     * @param \DateTime $to
     *
     * @return $this
     */
    public function setTo(\DateTime $to): self
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * setCreatedAt.
     *
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * getUpdatedAt.
     *
     * @return null|\DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * setUpdatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
