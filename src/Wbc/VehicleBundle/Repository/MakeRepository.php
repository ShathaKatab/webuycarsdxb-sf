<?php

namespace Wbc\VehicleBundle\Repository;

use Wbc\UtilityBundle\ORM\EntityRepository;
use Wbc\UtilityBundle\Request\Options\ListOptions;
use Wbc\VehicleBundle\Entity\Make;

/**
 * Class MakeRepository.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class MakeRepository extends EntityRepository
{
    /**
     * Override findAll() to always sort by `name`.
     *
     * @return array
     */
    public function findAll()
    {
        return $this->findBy(['active' => true], ['name' => 'ASC']);
    }

    public function findAllAsChoices()
    {
        $data = [];
        $makes = $this->findAll();
        /** @var Make $make */
        foreach ($makes as $make) {
            $data[$make->getId()] = $make->getName();
        }

        return $data;
    }

    /**
     * @param ListOptions $options
     *
     * @return array
     */
    public function findAllPaginated(ListOptions $options)
    {
        $qb = $this->_em->createQueryBuilder('make')
            ->select('make')
            ->from('WbcVehicleBundle:Make', 'make')
            ->where('make.active = :active')
            ->orderBy('make.name', 'ASC')
            ->setFirstResult($options->getOffset())
            ->setMaxResults($options->getLimit())
            ->setParameter(':active', true);

        return $this->getResults($qb);
    }
}
