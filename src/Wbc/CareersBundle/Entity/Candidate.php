<?php

declare(strict_types=1);

namespace Wbc\CareersBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Candidate.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Entity()
 * @ORM\Table(name="careers_candidate")
 */
class Candidate
{
    const STATUS_NEW = 'new';
    const STATUS_INTERVIEW_SCHEDULE_1 = 'interview-schedule-1';
    const STATUS_INTERVIEW_SCHEDULE_2 = 'interview-schedule-2';
    const STATUS_INTERVIEWED = 'interviewed';
    const STATUS_SHORTLISTED = 'shortlisted';
    const STATUS_HIRED = 'hired';
    const STATUS_REJECTED = 'rejected';

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
     * @ORM\Column(name="first_name", type="string", length=100)
     *
     * @Assert\NotBlank()
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=100)
     *
     * @Assert\NotBlank()
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email_address", type="string", length=100)
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $emailAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_number", type="string", length=15)
     *
     * @Assert\NotBlank()
     */
    protected $mobileNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="cover_letter", type="text", nullable=true)
     *
     * @Assert\Type("string")
     * @Assert\Length(max=255)
     */
    protected $coverLetter;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     */
    protected $uploadedCv;

    /**
     * @var string
     *
     * @ORM\Column(name="current_role", type="string", length=100, nullable=true)
     *
     * @Assert\Type("string")
     * @Assert\Length(max=100)
     */
    protected $currentRole;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="Wbc\CareersBundle\Entity\Role", inversedBy="candidates")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     *
     * @Assert\NotBlank()
     */
    protected $role;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=true)
     */
    protected $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="interview_at", type="datetime", nullable=true)
     */
    protected $interviewAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var File
     *
     * @Assert\File(
     *     maxSize = "5m",
     *     mimeTypes = {"application/pdf", "application/x-pdf", "application/msword"},
     * )
     */
    protected $uploadedFile;

    /**
     * Candidate constructor.
     *
     * @param Role $role
     */
    public function __construct(Role $role = null)
    {
        $this->role = $role;
        $this->status = self::STATUS_NEW;
    }

    public function __toString()
    {
        if ($this->id) {
            return (string) $this->id;
        }

        return '';
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_INTERVIEW_SCHEDULE_1 => 'Scheduled for Interview 1',
            self::STATUS_INTERVIEW_SCHEDULE_2 => 'Scheduled for Interview 2',
            self::STATUS_HIRED => 'Hired',
            self::STATUS_INTERVIEWED => 'Interviewed',
            self::STATUS_SHORTLISTED => 'Shortlisted',
            self::STATUS_HIRED => 'Hired',
            self::STATUS_REJECTED => 'Rejected',
        ];
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
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return Candidate
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName.
     *
     * @param string $lastName
     *
     * @return Candidate
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set emailAddress.
     *
     * @param string $emailAddress
     *
     * @return Candidate
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
     * Set mobileNumber.
     *
     * @param string $mobileNumber
     *
     * @return Candidate
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
     * Set coverLetter.
     *
     * @param string $coverLetter
     *
     * @return Candidate
     */
    public function setCoverLetter($coverLetter)
    {
        $this->coverLetter = $coverLetter;

        return $this;
    }

    /**
     * Get coverLetter.
     *
     * @return string
     */
    public function getCoverLetter()
    {
        return $this->coverLetter;
    }

    /**
     * Set currentRole.
     *
     * @param string $currentRole
     *
     * @return Candidate
     */
    public function setCurrentRole($currentRole)
    {
        $this->currentRole = $currentRole;

        return $this;
    }

    /**
     * Get currentRole.
     *
     * @return string
     */
    public function getCurrentRole()
    {
        return $this->currentRole;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Candidate
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

    /**
     * Set interviewAt.
     *
     * @param \DateTime $interviewAt
     *
     * @return Candidate
     */
    public function setInterviewAt($interviewAt)
    {
        $this->interviewAt = $interviewAt;

        return $this;
    }

    /**
     * Get interviewAt.
     *
     * @return \DateTime
     */
    public function getInterviewAt()
    {
        return $this->interviewAt;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Candidate
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
     * @return Candidate
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
     * Set uploadedCv.
     *
     * @param Media $uploadedCv
     *
     * @return Candidate
     */
    public function setUploadedCv(Media $uploadedCv = null)
    {
        $this->uploadedCv = $uploadedCv;

        return $this;
    }

    /**
     * Get uploadedCv.
     *
     * @return Media
     */
    public function getUploadedCv()
    {
        return $this->uploadedCv;
    }

    /**
     * Set role.
     *
     * @param Role $role
     *
     * @return Candidate
     */
    public function setRole(Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role.
     *
     * @return \Wbc\CareersBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return File
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * @param File $uploadedFile
     *
     * @return Candidate
     */
    public function setUploadedFile(File $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }
}
