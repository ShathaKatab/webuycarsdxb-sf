<?php

namespace Wbc\BranchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Wbc\BranchBundle\Form\DayType;

/**
 * Class Timing.
 *
 * @ORM\Table(name="branch_timing", uniqueConstraints={@ORM\UniqueConstraint(name="wbc_timing_unique_idx", columns={"branch_id",
 * "day_of_week", "from_time"})})
 * @ORM\Entity(repositoryClass="Wbc\BranchBundle\Repository\TimingRepository")
 *
 * @Serializer\ExclusionPolicy("all")*
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class Timing
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
     * @var int
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
     * @var int
     *
     * @ORM\Column(name="day_of_week", type="smallint")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose
     */
    protected $dayOfWeek;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="from_time", type="time")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose
     * @Serializer\Type(
     *   "DateTime<'h:i a'>"
     * )
     */
    protected $from;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="to_time", type="time")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose
     * @Serializer\Type(
     *   "DateTime<'h:i a'>"
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
     * Timing Constructor.
     *
     * @param Branch|null $branch
     * @param null $dayOfWeek
     * @param null $from
     * @param null $to
     */
    public function __construct(Branch $branch = null, $dayOfWeek = null, $from = null, $to=null)
    {
        $this->branch = $branch;
        $this->dayOfWeek = $dayOfWeek;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
    }

    /**
     * Set from.
     *
     * @param $from
     *
     * @return Timing
     */
    public function setFrom($from): self
    {
        if(is_string($from)){
            $from = date_create($from);
        }

        $this->from = $from;

        return $this;
    }

    /**
     * Get from.
     *
     * @return \DateTime|null|string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to.
     *
     * @param $to
     *
     * @return Timing
     */
    public function setTo($to): self
    {
        if(is_string($to)){
            $to = date_create($to);
        }

        $this->to = $to;

        return $this;
    }

    /**
     * Get to.
     *
     * @return \DateTime|null|string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Timing
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
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
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
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
    public function setBranch(Branch $branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch.
     *
     * @return Branch|null
     */
    public function getBranch(): ?Branch
    {
        return $this->branch;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("name")
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        if ($this->branch !== null && null !== $this->dayOfWeek && null !== $this->from && null !== $this->to) {
            return self::getNameStatic($this->branch->getName(), $this->dayOfWeek, $this->from, $this->to);
        }

        return null;
    }

    /**
     * getNameStatic.
     * @param string $branchName
     * @param int $dayOfWeek
     * @param \DateTime $from
     * @param \DateTime $to
     * @return string
     */
    public static function getNameStatic(string $branchName, int $dayOfWeek, \DateTime $from, \DateTime $to): string
    {
        return sprintf('%s - %s (%s - %s)', $branchName, DayType::getDays()[$dayOfWeek], $from->format('h:i a'), $to->format('h:i a'));
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("shortName")
     *
     * @return string|null
     */
    public function getShortName(): ?string
    {
        if (null !== $this->dayOfWeek && null !== $this->from && null !== $this->to) {
            return self::getShortNameStatic($this->dayOfWeek, $this->from, $this->to);
        }

        return null;
    }

    /**
     * getShortNameStatic.
     * @param int $dayOfWeek
     * @param \DateTime $from
     * @param \DateTime $to
     * @return string
     */
    public static function getShortNameStatic(int $dayOfWeek, \DateTime $from, \DateTime $to): string
    {
        return sprintf('%s (%s - %s)', DayType::getDays()[$dayOfWeek], $from->format('h:i a'), $to->format('h:i a'));
    }

    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get Timing string for FE.
     *
     * @return string|null
     */
    public function getTimingString(): ?string
    {
        if (null !== $this->from && null !== $this->to) {
            return sprintf('%s - %s', $this->from->format('h:i a'), $this->to->format('h:i a'));
        }

        return null;
    }

    /**
     * Formatted timing for the admin list view.
     *
     * @return string
     */
    public function getAdminListTiming()
    {
        return $this->getTimingString();
    }

    /**
     * Utility method to check if now has surpassed `from` of this Timing.
     *
     * @return bool
     */
    public function hasTimeSurpassed(): bool
    {
        return (new \DateTime())->format('H:i') > $this->from->format('H:i');
    }

    /**
     * getDayOfWeek.
     *
     * @return int|null
     */
    public function getDayOfWeek(): ?int
    {
        return $this->dayOfWeek;
    }

    /**
     * setDayOfWeek.
     *
     * @param int $dayOfWeek
     *
     * @return $this
     */
    public function setDayOfWeek(int $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    /**
     * setFromString.
     * @param $from
     * @return $this
     */
    public function setFromString($from): self
    {
        $this->setFrom(date_create($from));

        return $this;
    }

    /**
     * getFromString.
     * @return string|null
     */
    public function getFromString(): ?string
    {
        return $this->from !== null ? $this->from->format('H:i') : null;
    }

    /**
     * getToString.
     * @return string|null
     */
    public function getToString(): ?string
    {
        return $this->to !== null ? $this->to->format('H:i') : null;
    }

    /**
     * setToString.
     * @param $to
     * @return $this
     */
    public function setToString($to): self
    {
        $this->setTo(date_create($to));

        return $this;
    }
}
