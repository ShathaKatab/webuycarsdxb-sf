<?php

declare(strict_types=1);

namespace Wbc\InventoryBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Dealer.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @ORM\Table(name="dealer")
 * @ORM\Entity()
 */
class Dealer
{
    const TYPE_RETAIL = 'retail';
    const TYPE_DEALER = 'dealer';

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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile_number", type="string", length=15, nullable=true)
     */
    protected $mobileNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone_number", type="string", length=15, nullable=true)
     */
    protected $telephoneNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="email_address", type="string", length=100, nullable=true)
     */
    protected $emailAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="emirates_id", type="string", length=255, nullable=true)
     */
    protected $emiratesId;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     */
    protected $imageEmiratesId;

    /**
     * @var string
     *
     * @ORM\Column(name="name_company", type="string", length=255, nullable=true)
     */
    protected $nameCompany;

    /**
     * @var string
     *
     * @ORM\Column(name="number_trade_license", type="string", length=255, nullable=true)
     */
    protected $numberTradeLicense;

    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     */
    protected $imageTradeLicense;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    protected $address;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", options={"default": true})
     */
    protected $active;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=15, nullable=false)
     *
     * @Assert\Choice(choices={"dealer", "retail"})
     */
    protected $type;

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
     * Dealer constructor.
     */
    public function __construct()
    {
        $this->active = true;
    }

    public function __toString()
    {
        $name = $this->name;

        if (!$name) {
            $name = $this->nameCompany;
        }

        return $name ?: '';
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
     * Set name.
     *
     * @param string|null $name
     *
     * @return Dealer
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set mobileNumber.
     *
     * @param string|null $mobileNumber
     *
     * @return Dealer
     */
    public function setMobileNumber($mobileNumber = null)
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    /**
     * Get mobileNumber.
     *
     * @return string|null
     */
    public function getMobileNumber()
    {
        return $this->mobileNumber;
    }

    /**
     * Set emailAddress.
     *
     * @param string|null $emailAddress
     *
     * @return Dealer
     */
    public function setEmailAddress($emailAddress = null)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get emailAddress.
     *
     * @return string|null
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set emiratesId.
     *
     * @param string|null $emiratesId
     *
     * @return Dealer
     */
    public function setEmiratesId($emiratesId = null)
    {
        $this->emiratesId = $emiratesId;

        return $this;
    }

    /**
     * Get emiratesId.
     *
     * @return string|null
     */
    public function getEmiratesId()
    {
        return $this->emiratesId;
    }

    /**
     * Set nameCompany.
     *
     * @param string|null $nameCompany
     *
     * @return Dealer
     */
    public function setNameCompany($nameCompany = null)
    {
        $this->nameCompany = $nameCompany;

        return $this;
    }

    /**
     * Get nameCompany.
     *
     * @return string|null
     */
    public function getNameCompany()
    {
        return $this->nameCompany;
    }

    /**
     * Set numberTradeLicense.
     *
     * @param string|null $numberTradeLicense
     *
     * @return Dealer
     */
    public function setNumberTradeLicense($numberTradeLicense = null)
    {
        $this->numberTradeLicense = $numberTradeLicense;

        return $this;
    }

    /**
     * Get numberTradeLicense.
     *
     * @return string|null
     */
    public function getNumberTradeLicense()
    {
        return $this->numberTradeLicense;
    }

    /**
     * Set address.
     *
     * @param string|null $address
     *
     * @return Dealer
     */
    public function setAddress($address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Dealer
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
     * @return Dealer
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
     * Set active.
     *
     * @param bool $active
     *
     * @return Dealer
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set imageEmiratesId.
     *
     * @param Media|null $imageEmiratesId
     *
     * @return Dealer
     */
    public function setImageEmiratesId(Media $imageEmiratesId = null)
    {
        $this->imageEmiratesId = $imageEmiratesId;

        return $this;
    }

    /**
     * Get imageEmiratesId.
     *
     * @return Media|null
     */
    public function getImageEmiratesId()
    {
        return $this->imageEmiratesId;
    }

    /**
     * Set imageTradeLicense.
     *
     * @param Media|null $imageTradeLicense
     *
     * @return Dealer
     */
    public function setImageTradeLicense(Media $imageTradeLicense = null)
    {
        $this->imageTradeLicense = $imageTradeLicense;

        return $this;
    }

    /**
     * Get imageTradeLicense.
     *
     * @return Media|null
     */
    public function getImageTradeLicense()
    {
        return $this->imageTradeLicense;
    }

    /**
     * Set telephoneNumber.
     *
     * @param string|null $telephoneNumber
     *
     * @return Dealer
     */
    public function setTelephoneNumber($telephoneNumber = null)
    {
        $this->telephoneNumber = $telephoneNumber;

        return $this;
    }

    /**
     * Get telephoneNumber.
     *
     * @return string|null
     */
    public function getTelephoneNumber()
    {
        return $this->telephoneNumber;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return Dealer
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public static function getTypes()
    {
        return [self::TYPE_DEALER => 'Dealer', self::TYPE_RETAIL => 'Retail'];
    }
}
