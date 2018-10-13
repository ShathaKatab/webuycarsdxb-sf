<?php

declare(strict_types=1);

namespace Wbc\StaticBundle\Repository;

use Wbc\StaticBundle\Entity\Parameter;
use Wbc\UtilityBundle\ORM\EntityRepository;

/**
 * Class ParameterRepository.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class ParameterRepository extends EntityRepository
{
    /**
     * @var \Memcached
     */
    private $memcached;

    /**
     * @param $key
     *
     * @return null|Parameter
     */
    public function findOneByKey($key)
    {
        $fromCache = $this->memcached->get($key);

        if (false !== $fromCache) {
            return $fromCache;
        }

        $parameter = $this->findOneBy(['key' => $key]);

        if ($parameter) {
            $this->memcached->set($key, $parameter);
        }

        return $parameter;
    }

    public function setCacheManager(\Memcached $memcached): void
    {
        $this->memcached = $memcached;
    }
}
