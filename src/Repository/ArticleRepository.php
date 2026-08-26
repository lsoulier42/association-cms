<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Article>
 */
class ArticleRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }
}
