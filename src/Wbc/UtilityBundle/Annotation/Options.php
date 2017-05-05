<?php

namespace Wbc\UtilityBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * Class Options.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 *
 * @Annotation
 * @Target("METHOD")
 */
class Options extends ConfigurationAnnotation
{
    /**
     * The parameter name.
     *
     * @var string
     */
    protected $name;

    /**
     * The parameter class.
     *
     * @var string
     */
    protected $class;

    /**
     * Query string parameters.
     *
     * @var array
     */
    protected $requestParams = array();

    public function __construct(array $values)
    {
        parent::__construct($values);

        if (!class_exists($this->class)) {
            throw new \RuntimeException('Options annotation requires an existing class argument!');
        }
    }

    /**
     * Sets the class.
     *
     * @param string $class The class name
     */
    public function setValue($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name ?: 'options';
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return array
     */
    public function getRequestParams()
    {
        return $this->requestParams;
    }

    /**
     * @param array $requestParams
     */
    public function setRequestParams($requestParams)
    {
        $this->requestParams = $requestParams;

        return new $this->class($requestParams);
    }

    public function getAliasName()
    {
        return 'options';
    }

    /**
     * Multiple Options annotations are not allowed.
     *
     * @return bool
     */
    public function allowArray()
    {
        return false;
    }
}
