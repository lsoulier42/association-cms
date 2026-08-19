<?php

namespace App\Repository;

use App\Entity\BoardMember;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BoardMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoardMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoardMember[]    findAll()
 * @method BoardMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoardMemberRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoardMember::class);
    }
}
