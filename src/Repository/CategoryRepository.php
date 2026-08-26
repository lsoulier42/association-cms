<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Category>
 */
class CategoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return list<Category>
     */
    public function findMenuCategories(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.articles', 'a', 'WITH', 'a.showInMenu = :showInMenu')
            ->leftJoin('c.specialPages', 'sp', 'WITH', 'sp.showInMenu = :showInMenu')
            ->addSelect('a', 'sp')
            ->setParameter('showInMenu', true)
            ->orderBy('c.menuOrder', 'ASC')
            ->addOrderBy('c.name', 'ASC')
            ->addOrderBy('a.menuOrder', 'ASC')
            ->addOrderBy('a.publishedAt', 'DESC')
            ->addOrderBy('sp.menuOrder', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
