<?php

namespace Wbc\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Profile.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="fos_user_profile")
 * @ORM\Entity
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Profile
{
    /**
     * @var User
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Wbc\UserBundle\Entity\User", inversedBy="profile")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=60, nullable=true)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=60, nullable=true)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     */
    private $lastName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_of_birth", type="date", nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"owner-view", "profile-view"})
     */
    private $dateOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="gender_code", type="string", length=1, nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\Choice(choices={"m", "f"}, message="Choose a valid gender")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"owner-view", "profile-view"})
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="nationality_code", type="string", length=3, nullable=true)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"owner-view", "profile-view"})
     */
    private $nationality;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_number", type="string", length=25, nullable=true)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"owner-view", "profile-view"})
     */
    private $mobileNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="language_code", type="string", length=2, nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\Choice(choices={"en", "ar"}, message="Choose a valid language")
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"owner-view", "profile-view"})
     */
    private $language;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     *
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @var int
     *
     * @ORM\Column(name="city_id", type="integer", nullable=true)
     *
     * @Assert\NotBlank()
     *
     * @Serializer\Expose()
     * @Serializer\Groups({"owner-view", "profile-view"})
     */
    private $city;

    /**
     * @param User $user
     */
    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * Set firstName.
     *
     * @param string $firstName
     *
     * @return Profile
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
     * @return Profile
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
     * Set dateOfBirth.
     *
     * @param \DateTime $dateOfBirth
     *
     * @return Profile
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth.
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set gender.
     *
     * @param string $gender
     *
     * @return Profile
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender.
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set nationality (country ISO-3 code).
     *
     * @param string $nationality
     *
     * @return Profile
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Get nationality.
     *
     * @return string
     */
    public function getNationality()
    {
        //@todo: return the nationality name
        return $this->nationality;
    }

    /**
     * Set mobileNumber.
     *
     * @param string $mobileNumber
     *
     * @return Profile
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
     * Set language (language ISO-3 code).
     *
     * @param string $language
     *
     * @return Profile
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language.
     *
     * @return string
     */
    public function getLanguage()
    {
        //@todo: return the language name
        return $this->language;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return Profile
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city (city model).
     *
     * @param mixed $city
     *
     * @return Profile
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return \Wbc\UtilityBundle\Model\City
     */
    public function getCity()
    {
        //@todo: return the city model
        return $this->city;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return Profile
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
