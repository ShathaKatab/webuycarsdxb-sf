<?php

declare(strict_types=1);

namespace Wbc\CareersBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Role.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Entity(repositoryClass="Wbc\CareersBundle\Repository\RoleRepository")
 * @ORM\Table(name="careers_role")
 */
class Role
{
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
     * @ORM\Column(name="title", type="string", length=100)
     *
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=100)
     *
     * @Assert\NotBlank()
     */
    protected $location;

    /**
     * @var string
     *
     * @ORM\Column(name="department", type="string", length=100)
     *
     * @Assert\NotBlank()
     */
    protected $department;

    /**
     * @var string
     *
     * @ORM\Column(name="responsibilities", type="text", nullable=true)
     */
    protected $responsibilities;

    /**
     * @var string
     *
     * @ORM\Column(name="skills_and_experience", type="text", nullable=true)
     */
    protected $skillsAndExperience;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Wbc\CareersBundle\Entity\Candidate", mappedBy="role")
     */
    protected $candidates;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publish_at", type="datetime")
     *
     * @Assert\Date()
     */
    protected $publishAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     *
     * @Assert\Date()
     */
    protected $expiresAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true, options={"default": true})
     */
    protected $isActive;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=128, unique=true)
     *
     * @Gedmo\Slug(separator="-", fields={"title"})
     */
    protected $slug;

    /**
     * Role constructor.
     */
    public function __construct()
    {
        $this->publishAt = new \DateTime();
        $this->isActive = true;
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
     * Set title.
     *
     * @param string $title
     *
     * @return Role
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set location.
     *
     * @param string $location
     *
     * @return Role
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location.
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set responsibilities.
     *
     * @param string $responsibilities
     *
     * @return Role
     */
    public function setResponsibilities($responsibilities)
    {
        $this->responsibilities = $responsibilities;

        return $this;
    }

    /**
     * Get responsibilities.
     *
     * @return string
     */
    public function getResponsibilities()
    {
        return $this->responsibilities;
    }

    /**
     * Set skillsAndExperience.
     *
     * @param string $skillsAndExperience
     *
     * @return Role
     */
    public function setSkillsAndExperience($skillsAndExperience)
    {
        $this->skillsAndExperience = $skillsAndExperience;

        return $this;
    }

    /**
     * Get skillsAndExperience.
     *
     * @return string
     */
    public function getSkillsAndExperience()
    {
        return $this->skillsAndExperience;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Role
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set publishAt.
     *
     * @param \DateTime $publishAt
     *
     * @return Role
     */
    public function setPublishAt($publishAt)
    {
        $this->publishAt = $publishAt;

        return $this;
    }

    /**
     * Get publishAt.
     *
     * @return \DateTime
     */
    public function getPublishAt()
    {
        return $this->publishAt;
    }

    /**
     * Set expiresAt.
     *
     * @param \DateTime $expiresAt
     *
     * @return Role
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get expiresAt.
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return Role
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Role
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
     * @return Role
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
     * Add candidates.
     *
     * @param \Wbc\CareersBundle\Entity\Candidate $candidates
     *
     * @return Role
     */
    public function addCandidate(\Wbc\CareersBundle\Entity\Candidate $candidates)
    {
        $this->candidates[] = $candidates;

        return $this;
    }

    /**
     * Remove candidates.
     *
     * @param \Wbc\CareersBundle\Entity\Candidate $candidates
     */
    public function removeCandidate(\Wbc\CareersBundle\Entity\Candidate $candidates): void
    {
        $this->candidates->removeElement($candidates);
    }

    /**
     * Get candidates.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCandidates()
    {
        return $this->candidates;
    }

    public function getTotalCandidates()
    {
        return $this->candidates->count();
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Role
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set department.
     *
     * @param string $department
     *
     * @return Role
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department.
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    public static function getCities()
    {
        return [
            'dubai' => 'Dubai',
            'abu-dhabi' => 'Abu Dhabi',
            'al-ain' => 'Al Ain',
            'sharjah' => 'Sharjah',
            'ajman' => 'Ajman',
            'umm-al-quwain' => 'Umm Al Quwain',
            'ras-al-khaimah' => 'Ras Al Khaimah',
        ];
    }

    public static function getDepartments()
    {
        return [
            'sales' => 'Sales',
            'call-center' => 'Call Center',
            'purchasing' => 'Purchasing',
        ];
    }
}
