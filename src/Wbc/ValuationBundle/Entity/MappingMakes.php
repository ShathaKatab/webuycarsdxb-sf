<?php

namespace Wbc\ValuationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wbc\VehicleBundle\Entity\Make;

/**
 * Class MappingMakes.
 *
 * @ORM\Table(name="mapping_makes")
 * @ORM\Entity()
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class MappingMakes
{
    /**
     * @var Make
     *
     * @ORM\ManyToOne(targetEntity="\Wbc\VehicleBundle\Entity\Make")
     * @ORM\JoinColumn(name="make_id", referencedColumnName="id")
     * @ORM\Id()
     */
    protected $make;

    /**
     * @var string
     *
     * @ORM\Column(name="make_name", type="string", length=60)
     */
    protected $makeName;

    /**
     * @var string
     *
     * @ORM\Column(name="get_that_make_name", type="string", length=60, nullable=true)
     */
    protected $getThatMakeName;

    /**
     * @var string
     *
     * @ORM\Column(name="dubizzle_make_name", type="string", length=60, nullable=true)
     */
    protected $dubizzleMakeName;

    /**
     * MappingMakes Constructor.
     *
     * @param Make $make
     */
    public function __construct($make)
    {
        $this->make = $make;
    }

    /**
     * Set makeName.
     *
     * @param string $makeName
     *
     * @return MappingMakes
     */
    public function setMakeName($makeName)
    {
        $this->makeName = $makeName;

        return $this;
    }

    /**
     * Get makeName.
     *
     * @return string
     */
    public function getMakeName()
    {
        return $this->makeName;
    }

    /**
     * Set getThatMakeName.
     *
     * @param string $getThatMakeName
     *
     * @return MappingMakes
     */
    public function setGetThatMakeName($getThatMakeName)
    {
        $this->getThatMakeName = $getThatMakeName;

        return $this;
    }

    /**
     * Get getThatMakeName.
     *
     * @return string
     */
    public function getGetThatMakeName()
    {
        return $this->getThatMakeName;
    }

    /**
     * Set dubizzleMakeName.
     *
     * @param string $dubizzleMakeName
     *
     * @return MappingMakes
     */
    public function setDubizzleMakeName($dubizzleMakeName)
    {
        $this->dubizzleMakeName = $dubizzleMakeName;

        return $this;
    }

    /**
     * Get dubizzleMakeName.
     *
     * @return string
     */
    public function getDubizzleMakeName()
    {
        return $this->dubizzleMakeName;
    }

    /**
     * Set make.
     *
     * @param Make $make
     *
     * @return MappingMakes
     */
    public function setMake(Make $make)
    {
        $this->make = $make;

        return $this;
    }

    /**
     * Get make.
     *
     * @return Make
     */
    public function getMake()
    {
        return $this->make;
    }
}
