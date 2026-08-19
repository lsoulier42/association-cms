<?php

namespace App\Repository;

use App\Entity\Partner;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Partner>
 */
class PartnerRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partner::class);
    }

    /**
     * @return Partner[]
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.position', 'ASC')
            ->addOrderBy('p.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
