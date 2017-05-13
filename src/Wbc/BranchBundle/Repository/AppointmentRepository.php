<?php

namespace Wbc\BranchBundle\Repository;

use Wbc\BranchBundle\Entity\Appointment;
use Wbc\UtilityBundle\ORM\EntityRepository;

/**
 * Class AppointmentRepository.
 *
 * @author Majid Mvulle <majid@majidmvulle.com>
 */
class AppointmentRepository extends EntityRepository
{
    /**
     * Gets total Appointments.
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function totalAppointments()
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('count(a.id)')
            ->from('WbcBranchBundle:Appointment', 'a')
            ->where($queryBuilder->expr()->notIn('a.status', [Appointment::STATUS_INVALID]));

        return intval($queryBuilder->getQuery()->getSingleScalarResult());
    }
}
