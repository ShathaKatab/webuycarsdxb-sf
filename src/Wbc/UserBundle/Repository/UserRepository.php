<?php

declare(strict_types=1);

namespace Wbc\UserBundle\Repository;

use Wbc\UserBundle\Entity\User;
use Wbc\UtilityBundle\ORM\EntityRepository;

/**
 * Class UserRepository.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class UserRepository extends EntityRepository
{
    /**
     * @param string $role
     *
     * @return array
     */
    public function findAllByRole($role)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('u')
            ->from(User::class, 'u')
            ->where($queryBuilder->expr()->like('u.roles', $queryBuilder->expr()->literal('%'.$role.'%')));

        return $queryBuilder->getQuery()->getResult();
    }
}
