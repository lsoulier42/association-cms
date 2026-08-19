<?php

namespace App\Repository;

use App\Entity\PressMention;
use Doctrine\Persistence\ManagerRegistry;

class PressMentionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PressMention::class);
    }
}
