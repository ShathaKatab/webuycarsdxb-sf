<?php

namespace Wbc\BranchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Timing.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="branch_timing")
 * @ORM\Entity
 */
class Timing
{
    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\BranchBundle\Entity\Branch", inversedBy="timings", fetch="EAGER")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id")
     * @ORM\Id
     *
     * @Assert\NotBlank()
     */
    protected $branch;

    /**
     * ISO-8601 numeric representation of the day of the week (1 - Monday, 7 - Sunday).
     *
     * @var int
     *
     * @ORM\Column(name="day", type="smallint")
     * @ORM\Id
     *
     * @Assert\NotBlank()
     */
    protected $day;

    /**
     * Time (format => hour * 60 + minutes).
     *
     * @var int
     *
     * @ORM\Column(name="from_time", type="smallint")
     * @ORM\Id
     *
     * @Assert\NotBlank()
     */
    protected $from;

    /**
     * Time (format => hour * 60 + minutes).
     *
     * @var int
     *
     * @ORM\Column(name="to_time", type="smallint")
     *
     * @Assert\NotBlank()
     */
    protected $to;

    /**
     * @var int
     *
     * @ORM\Column(name="number_of_slots", type="smallint", options={"default": 0})
     *
     * @Assert\NotBlank()
     */
    protected $numberOfSlots;

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
     * Set day.
     *
     * @param int $day
     *
     * @return Timing
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day.
     *
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set from.
     *
     * @param string $from
     *
     * @return Timing
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to.
     *
     * @param string $to
     *
     * @return Timing
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to.
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set numberOfSlots.
     *
     * @param int $numberOfSlots
     *
     * @return Timing
     */
    public function setNumberOfSlots($numberOfSlots)
    {
        $this->numberOfSlots = $numberOfSlots;

        return $this;
    }

    /**
     * Get numberOfSlots.
     *
     * @return int
     */
    public function getNumberOfSlots()
    {
        return $this->numberOfSlots;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Timing
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
     * @return Timing
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
     * Set branch.
     *
     * @param Branch $branch
     *
     * @return Timing
     */
    public function setBranch(Branch $branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch.
     *
     * @return Branch
     */
    public function getBranch()
    {
        return $this->branch;
    }
}
