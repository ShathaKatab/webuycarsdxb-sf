<?php

declare(strict_types=1);

namespace Wbc\BranchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AppointmentReminder.
 *
 * @ORM\Table(name="appointment_reminder")
 * @ORM\Entity()
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class AppointmentReminder
{
    const STATUS_NEW = 'new';
    const STATUS_CALLED_BACK = 'called-back';
    const STATUS_REJECTED = 'rejected';

    /**
     * @var Appointment
     *
     * @ORM\OneToOne(targetEntity="\Wbc\BranchBundle\Entity\Appointment", inversedBy="appointmentReminder")
     * @ORM\JoinColumn(name="appointment_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\Id
     *
     * @Assert\NotBlank()
     */
    protected $appointment;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_number", type="string", length=15)
     *
     * @Assert\NotBlank()
     */
    protected $mobileNumber;

    /**
     * @var bool
     *
     * @ORM\Column(name="reschedule",  type="boolean", nullable=true, options={"default": false})
     */
    protected $isReschedule = false;

    /**
     * @var string
     *
     * @ORM\Column(name="response_text", type="text", nullable=true)
     *
     * @Assert\NotBlank()
     */
    protected $responseText;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, options={"default": "new"})
     *
     * @Assert\NotBlank()
     */
    protected $status;

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
     * AppointmentReminder constructor.
     *
     * @param Appointment $appointment
     * @param string      $mobileNumber
     * @param bool        $isReschedule
     * @param string      $responseText
     */
    public function __construct(Appointment $appointment, string $mobileNumber, bool $isReschedule = false, string $responseText = '')
    {
        $this->appointment = $appointment;
        $this->mobileNumber = $mobileNumber;
        $this->isReschedule = $isReschedule;
        $this->responseText = $responseText;
        $this->status = self::STATUS_NEW;
    }

    /**
     * Set mobileNumber.
     *
     * @param string $mobileNumber
     *
     * @return AppointmentReminder
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
     * Set isReschedule.
     *
     * @param bool $isReschedule
     *
     * @return AppointmentReminder
     */
    public function setIsReschedule($isReschedule)
    {
        $this->isReschedule = $isReschedule;

        return $this;
    }

    /**
     * Get isReschedule.
     *
     * @return bool
     */
    public function isReschedule()
    {
        return $this->isReschedule;
    }

    /**
     * Set responseText.
     *
     * @param string $responseText
     *
     * @return AppointmentReminder
     */
    public function setResponseText($responseText)
    {
        $this->responseText = $responseText;

        return $this;
    }

    /**
     * Get responseText.
     *
     * @return string
     */
    public function getResponseText()
    {
        return $this->responseText;
    }

    /**
     * Set appointment.
     *
     * @param Appointment $appointment
     *
     * @return AppointmentReminder
     */
    public function setAppointment(Appointment $appointment)
    {
        $this->appointment = $appointment;

        return $this;
    }

    /**
     * Get appointment.
     *
     * @return Appointment
     */
    public function getAppointment()
    {
        return $this->appointment;
    }

    /**
     * Get isReschedule.
     *
     * @return bool
     */
    public function getIsReschedule()
    {
        return $this->isReschedule;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return AppointmentReminder
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
     * @return AppointmentReminder
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
     * Set status.
     *
     * @param string $status
     *
     * @return AppointmentReminder
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_CALLED_BACK => 'Called Back',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }
}
