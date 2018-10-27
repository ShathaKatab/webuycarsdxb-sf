<?php

declare(strict_types=1);

namespace Wbc\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * User.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="fos_user", uniqueConstraints={@ORM\UniqueConstraint(name="wbc_user_unique_idx", columns={"username",
 * "username_canonical", "email", "email_canonical"})})
 * @ORM\Entity(repositoryClass="Wbc\UserBundle\Repository\UserRepository")
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
     * @var Profile
     *
     * @ORM\OneToOne(targetEntity="Wbc\UserBundle\Entity\Profile", mappedBy="user", cascade={"persist"}, fetch="EAGER")
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
     * @var bool
     */
    protected $admin;

    /**
     * @var string
     */
    protected $fullName;

    public function __construct()
    {
        parent::__construct();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->profile = new Profile($this);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getFullName();
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
     *
     * @param mixed $serialized
     */
    public function unserialize($serialized): void
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

    public function setFullName($fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('ROLE_SUPER_ADMIN');
    }

    /**
     * @param bool $isAdmin
     */
    public function setAdmin($isAdmin): void
    {
        $this->admin = $isAdmin;

        if (true === (bool) $isAdmin) {
            $this->addRole('ROLE_SUPER_ADMIN');
        } else {
            $this->removeRole('ROLE_SUPER_ADMIN');
        }
    }

    /**
     * @return array
     */
    public static function getBaseRoles()
    {
        return [
            'ROLE_APPOINTMENT_SMS',
            'ROLE_BLOG_EDITOR',
            'ROLE_ACCOUNTANT',
            'ROLE_PURCHASING',
            'ROLE_CALL_CENTER',
            'ROLE_SUPER_ADMIN',
            'ROLE_CAREERS_EDITOR',
            'ROLE_USED_CARS_EDITOR',
            'ROLE_MARKETING',
        ];
    }
}
