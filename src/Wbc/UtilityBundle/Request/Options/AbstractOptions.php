<?php

namespace Wbc\UtilityBundle\Request\Options;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractOptions.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
abstract class AbstractOptions implements OptionsInterface
{
    /**
     * @var OptionsResolver
     */
    private $resolver;

    protected $options = array();

    public function __construct(array $options = array())
    {
        $this->resolver = new OptionsResolver();
        $this->configureOptions($this->resolver);
        $this->options = $this->resolver->resolve($options);
    }

    public function has($option)
    {
        return isset($this->options[$option]);
    }

    public function get($option)
    {
        if (!$this->has($option)) {
            return null;
        }

        return $this->options[$option];
    }
}
