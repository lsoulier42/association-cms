<?php

namespace App\Repository;

use App\Entity\SpecialPage;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<SpecialPage>
 */
class SpecialPageRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpecialPage::class);
    }
}
