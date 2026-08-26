<?php

namespace App\Repository;

use App\Entity\PressMention;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<PressMention>
 */
class PressMentionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PressMention::class);
    }
}
