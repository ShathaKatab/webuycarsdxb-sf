<?php

namespace Wbc\ValuationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Wbc\VehicleBundle\Entity\Model;

/**
 * Class MappingModels.
 *
 * @ORM\Table(name="mapping_models")
 * @ORM\Entity()
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class MappingModels
{
    /**
     * @var Model
     *
     * @ORM\OneToOne(targetEntity="\Wbc\VehicleBundle\Entity\Model")
     * @ORM\JoinColumn(name="model_id", referencedColumnName="id")
     * @ORM\Id()
     */
    protected $model;

    /**
     * @var string
     *
     * @ORM\Column(name="model_name", type="string", length=60)
     */
    protected $modelName;

    /**
     * @var string
     *
     * @ORM\Column(name="get_that_model_name", type="string", length=60, nullable=true)
     */
    protected $getThatModelName;

    /**
     * @var string
     *
     * @ORM\Column(name="dubizzle_model_name", type="string", length=60, nullable=true)
     */
    protected $dubizzleModelName;

    /**
     * @var string
     *
     * @ORM\Column(name="manheim_model_name", type="string", length=60, nullable=true)
     */
    protected $manheimModelName;

    /**
     * MappingModels Constructor.
     *
     * @param Model $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Set modelName.
     *
     * @param string $modelName
     *
     * @return MappingModels
     */
    public function setModelName($modelName)
    {
        $this->modelName = $modelName;

        return $this;
    }

    /**
     * Get modelName.
     *
     * @return string
     */
    public function getModelName()
    {
        return $this->modelName;
    }

    /**
     * Set getThatModelName.
     *
     * @param string $getThatModelName
     *
     * @return MappingModels
     */
    public function setGetThatModelName($getThatModelName)
    {
        $this->getThatModelName = $getThatModelName;

        return $this;
    }

    /**
     * Get getThatModelName.
     *
     * @return string
     */
    public function getGetThatModelName()
    {
        return $this->getThatModelName;
    }

    /**
     * Set dubizzleModelName.
     *
     * @param string $dubizzleModelName
     *
     * @return MappingModels
     */
    public function setDubizzleModelName($dubizzleModelName)
    {
        $this->dubizzleModelName = $dubizzleModelName;

        return $this;
    }

    /**
     * Get dubizzleModelName.
     *
     * @return string
     */
    public function getDubizzleModelName()
    {
        return $this->dubizzleModelName;
    }

    /**
     * Set model.
     *
     * @param Model $model
     *
     * @return MappingModels
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set manheimModelName.
     *
     * @param string $manheimModelName
     *
     * @return MappingModels
     */
    public function setManheimModelName($manheimModelName)
    {
        $this->manheimModelName = $manheimModelName;

        return $this;
    }

    /**
     * Get manheimModelName.
     *
     * @return string
     */
    public function getManheimModelName()
    {
        return $this->manheimModelName;
    }
}
