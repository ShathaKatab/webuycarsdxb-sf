<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Wbc\UsedCarsBundle\Entity\UsedCars;
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_number", type="string", length=15, nullable=true)
     *
     * @Assert\NotBlank()
     */
    protected $mobileNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="email_address", type="string", length=100, nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $emailAddress;

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
     * @var UsedCars
     *
     * @ORM\OneToOne(targetEntity="Wbc\UsedCarsBundle\Entity\UsedCars", mappedBy="deal")
     */
    protected $usedCar;

    /**
     * Deal Constructor.
     *
     * @param Inspection $inspection
     */
    public function __construct(Inspection $inspection = null)
    {
        $this->setInspection($inspection);
    }

    public function __toString()
    {
        if ($this->id) {
            return (string) $this->id;
        }

        return '';
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

        if ($inspection) {
            $appointment = $inspection->getAppointment();

            if ($appointment) {
                $this->name = $appointment->getName();
                $this->emailAddress = $appointment->getEmailAddress();
                $this->mobileNumber = $appointment->getMobileNumber();
            }
        }

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

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Deal
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
     * Set mobileNumber.
     *
     * @param string $mobileNumber
     *
     * @return Deal
     */
    public function setMobileNumber($mobileNumber)
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    /**
     * Get mobileNumber.
     *
     * @return string
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * Set emailAddress.
     *
     * @param string $emailAddress
     *
     * @return Deal
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get emailAddress.
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Gets Appointment.
     *
     * @return Appointment
     */
    public function getAppointment()
    {
        if ($this->inspection) {
            return $this->inspection->getAppointment();
        }
    }

    /**
     * Gets Timing String.
     *
     * @return string
     */
    public function getTimingString()
    {
        $appointment = $this->getAppointment();

        if ($appointment) {
            $branchTiming = $appointment->getBranchTiming();

            if ($branchTiming) {
                return $branchTiming->getTimingString();
            }
        }
    }

    /**
     * Gets date booked.
     *
     * @return string
     */
    public function getDateBookedString()
    {
        $appointment = $this->getAppointment();

        if ($appointment) {
            $dateBooked = $appointment->getDateBooked();

            if ($dateBooked) {
                return $dateBooked->format('M d, Y');
            }
        }
    }

    public function getAppointmentName()
    {
        $appointment = $this->getAppointment();

        if ($appointment) {
            return $appointment->getName();
        }
    }

    public function getAppointmentMobileNumber()
    {
        $appointment = $this->getAppointment();

        if ($appointment) {
            return $appointment->getMobileNumber();
        }
    }

    public function getAppointmentEmailAddress()
    {
        $appointment = $this->getAppointment();

        if ($appointment) {
            return $appointment->getEmailAddress();
        }
    }

    public function getAppointmentBranch()
    {
        $appointment = $this->getAppointment();

        if ($appointment) {
            return $appointment->getBranch();
        }
    }

    public function getAppointmentDayBooked()
    {
        $appointment = $this->getAppointment();

        if ($appointment) {
            return $appointment->getDayBooked();
        }
    }

    public function getAppointmentDateBooked()
    {
        $appointment = $this->getAppointment();

        if ($appointment) {
            return $appointment->getDateBooked();
        }
    }

    public function getAppointmentBranchTiming()
    {
        $appointment = $this->getAppointment();

        if ($appointment) {
            return $appointment->getBranchTiming();
        }
    }

    public function getAppointmentNotes()
    {
        $appointment = $this->getAppointment();

        if ($appointment) {
            return $appointment->getNotes();
        }
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return Deal
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
     * Set usedCar.
     *
     * @param UsedCars $usedCar
     *
     * @return Deal
     */
    public function setUsedCar(UsedCars $usedCar = null)
    {
        $this->usedCar = $usedCar;

        return $this;
    }

    /**
     * Get usedCar.
     *
     * @return UsedCars
     */
    public function getUsedCar()
    {
        return $this->usedCar;
    }
}
