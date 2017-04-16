<?php

namespace Wbc\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * User.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="fos_user", uniqueConstraints={@ORM\UniqueConstraint(name="wbc_user_unique_idx", columns={"username",
 * "username_canonical", "email", "email_canonical"})})
 * @ORM\Entity
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="username", column=@ORM\Column(name="username", length=60)),
 *      @ORM\AttributeOverride(name="usernameCanonical", column=@ORM\Column(name="username_canonical", length=60)),
 *      @ORM\AttributeOverride(name="password", column=@ORM\Column(name="password", length=64)),
 *      @ORM\AttributeOverride(name="email", column=@ORM\Column(name="email", length=60)),
 *      @ORM\AttributeOverride(name="emailCanonical", column=@ORM\Column(name="email_canonical", length=60))
 * })
 *
 * @Serializer\ExclusionPolicy("all")
 */
class User extends BaseUser
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
     */
    protected $username;

    /**
     * @var string
     */
    protected $usernameCanonical;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     *
     * @Serializer\Expose
     * @Serializer\Groups({"owner-view"})
     */
    protected $email;

    /**
     * @var string
     */
    protected $emailCanonical;

    /**
     * @var Profile
     *
     * @ORM\OneToOne(targetEntity="Wbc\UserBundle\Entity\Profile", mappedBy="user", cascade={"persist"})
     *
     * @Serializer\Expose
     */
    protected $profile;

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
     * @var string
     */
    protected $fullName;

    public function __construct()
    {
        parent::__construct();
        $this->listings = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return User
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
     * @return User
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
     * Set profile.
     *
     * @param \wbc\UserBundle\Entity\Profile $profile
     *
     * @return User
     */
    public function setProfile(Profile $profile = null)
    {
        $this->profile = $profile;

        if ($profile) {
            $profile->setUser($this);
        }

        return $this;
    }

    /**
     * Get profile.
     *
     * @return \wbc\UserBundle\Entity\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
            $this->username,
            $this->usernameCanonical,
            $this->enabled,
            $this->profile,
            $this->createdAt,
            $this->updatedAt,
            $this->lastLogin,
            $this->roles,
        ]);
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $data = array_merge($data, array_fill(0, 2, null));
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
            $this->username,
            $this->usernameCanonical,
            $this->enabled,
            $this->profile,
            $this->createdAt,
            $this->updatedAt,
            $this->lastLogin,
            $this->roles) = $data;
    }

    public function getFullName()
    {
        $fullName = $this->fullName;
        $profile = $this->getProfile();

        if ($profile && !$fullName) {
            $fullName = sprintf('%s %s', $profile->getFirstName(), $profile->getLastName());
        }

        return $fullName;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }
}