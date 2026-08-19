<?php

namespace App\Repository;

use App\Entity\Association;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Association>
 */
class AssociationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Association::class);
    }

    public function getSettings(): ?Association
    {
        return $this->findOneBy([]);
    }
}
