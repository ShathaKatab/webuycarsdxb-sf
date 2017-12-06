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
 * "day_booked", "from_time"})})
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
     * ISO-8601 numeric representation of the day of the week (1 - Monday, 7 - Sunday).
     *
     * @var int
     *
     * @ORM\Column(name="day_booked", type="smallint")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose
     */
    protected $dayBooked;

    /**
     * Time (format => hour * 60 + minutes).
     *
     * @var int
     *
     * @ORM\Column(name="from_time", type="smallint")
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose
     * @Serializer\Type("string")
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
     *
     * @Serializer\Expose
     * @Serializer\Type("string")
     */
    protected $to;

    /**
     * @var int
     *
     * @ORM\Column(name="number_of_slots", type="smallint", options={"default": 0})
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose
     */
    protected $numberOfSlots;

    /**
     * @Serializer\Expose
     *
     * @fixme: Remove this hardcoded value
     */
    protected $availableSlots = 3;

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
     * @var bool
     *
     * @ORM\Column(name="admin_only", type="boolean", nullable=true, options={"default": false})
     */
    protected $adminOnly;

    /**
     * Timing Constructor.
     *
     * @param Branch $branch
     * @param int    $dayBooked
     * @param int    $from
     */
    public function __construct(Branch $branch = null, $dayBooked = null, $from = null)
    {
        $this->branch = $branch;
        $this->dayBooked = $dayBooked;
        $this->from = $from;
        $this->adminOnly = false;
    }

    public function __toString()
    {
        return $this->getName() ?: '';
    }

    /**
     * Set dayBooked.
     *
     * @param int $dayBooked
     *
     * @return Timing
     */
    public function setDayBooked($dayBooked)
    {
        $this->dayBooked = $dayBooked;

        return $this;
    }

    /**
     * Get dayBooked.
     *
     * @return int
     */
    public function getDayBooked()
    {
        return $this->dayBooked;
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

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("name")
     *
     * @return string
     */
    public function getName()
    {
        if ($this->branch && null !== $this->dayBooked && null !== $this->from && null !== $this->to) {
            if ($this->adminOnly) {
                return sprintf('%s - %s (Walk-In)', $this->branch->getName(), DayType::getDays()[$this->dayBooked]);
            }

            return sprintf('%s - %s (%s - %s)', $this->branch->getName(), DayType::getDays()[$this->dayBooked], $this->from, $this->to);
        }
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("shortName")
     *
     * @return string
     */
    public function getShortName()
    {
        if (null !== $this->dayBooked && null !== $this->from && null !== $this->to) {
            return sprintf('%s (%s - %s)', DayType::getDays()[$this->dayBooked], $this->from, $this->to);
        }
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
     * Get Timing string for FE.
     *
     * @return string
     */
    public function getTimingString()
    {
        if (null !== $this->from && null !== $this->to) {
            if ($this->adminOnly) {
                return 'Walk-In';
            }

            return sprintf('%s - %s', self::formatIntegerToTimeString($this->from), self::formatIntegerToTimeString($this->to));
        }
    }

    /**
     * @param int $integerTime
     *
     * @return string
     */
    public static function formatIntegerToTimeString($integerTime)
    {
        if (is_int($integerTime)) {
            $timeString = sprintf('%02d:%02d', (int) ($integerTime / 60), (int) ($integerTime % 60));

            return strtoupper((new \DateTime($timeString))->format('h:i a'));
        }

        return $integerTime;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return int
     */
    public static function formatDateTimeToInteger(\DateTime $dateTime)
    {
        return (int) ($dateTime->format('H')) * 60 + (int) ($dateTime->format('i'));
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
    public function hasTimeSurpassed()
    {
        return $this->formatDateTimeToInteger(new \DateTime()) > $this->formatDateTimeToInteger(new \DateTime($this->from));
    }

    /**
     * Set adminOnly.
     *
     * @param bool $adminOnly
     *
     * @return Timing
     */
    public function setAdminOnly($adminOnly)
    {
        $this->adminOnly = $adminOnly;

        return $this;
    }

    /**
     * Get adminOnly.
     *
     * @return bool
     */
    public function getAdminOnly()
    {
        return $this->adminOnly;
    }

    /**
     * Is adminOnly.
     *
     * @return bool
     */
    public function isAdminOnly()
    {
        return $this->adminOnly;
    }
}
