<?php

namespace App\Repository;

use App\Entity\LinkedInPost;
use Doctrine\Persistence\ManagerRegistry;

class LinkedInPostRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LinkedInPost::class);
    }
}
