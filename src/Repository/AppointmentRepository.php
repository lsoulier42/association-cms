<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Appointment>
 */
class AppointmentRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }
}
