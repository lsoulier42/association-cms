<?php

namespace App\Repository;

use App\Dto\PaginationDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @template T of object
 * @extends ServiceEntityRepository<T>
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @param class-string<T> $className
     */
    public function __construct(ManagerRegistry $registry, string $className)
    {
        parent::__construct($registry, $className);
    }

    /**
     * @param T $entity
     * @param bool $flush
     */
    public function createOrUpdate(mixed $entity, bool $flush = true): void
    {
        $entityManager = $this->getEntityManager();
        if ($entity->getId() === null) {
            $entityManager->persist($entity);
        }
        if ($flush) {
            $entityManager->flush();
        }
    }

    /**
     * @param T $entity
     * @param bool $flush
     */
    public function remove(mixed $entity, bool $flush = true): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($entity);
        if ($flush) {
            $entityManager->flush();
        }
    }

    /**
     * @return Collection<int, T>
     */
    public static function getCollectionFromQueryBuilder(QueryBuilder $queryBuilder): Collection
    {
        return new ArrayCollection($queryBuilder->getQuery()->getResult());
    }

    /**
     * @return Pagerfanta<T>
     */
    public static function findAllPaginated(PaginationDto $dto, QueryBuilder $queryBuilder): Pagerfanta
    {
        $adapter = new QueryAdapter($queryBuilder);
        $pagerFanta = new Pagerfanta($adapter);
        $pagerFanta->setMaxPerPage($dto->getLimit())
            ->setCurrentPage($dto->getPage());
        return $pagerFanta;
    }
}
