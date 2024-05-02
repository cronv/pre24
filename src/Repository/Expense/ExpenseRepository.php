<?php

namespace cronv\Task\Management\Repository\Expense;

use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\Entity\Expense\Expense;
use cronv\Task\Management\Entity\Survey\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for expense information.
 *
 * @extends ServiceEntityRepository<Expense> Optional base class ServiceEntityRepository with a simplified constructor
 * (for auto-wiring).
 *
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null) Finds an entity by its primary key/identifier.
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null) Finds a single entity by a set of criteria.
 * @method Expense[]    findAll() Finds all entities in the repository.
 * @method Expense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) Finds entities
 * by a set of criteria.
 */
class ExpenseRepository extends ServiceEntityRepository
{
    protected int $limit = 10;

    /**
     * {@inheritDoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    /**
     * Search (paginator)
     *
     * @param array $params Params
     * @return PaginatorDTO
     */
    public function findPaginatedResults(array $params): PaginatorDTO
    {
        $page = $params['page'];
        $userId = $params['userId'];
        $limit = $this->limit;

        $queryBuilder = $this->createQueryBuilder('e')
            ->select('e')
            ->orderBy('e.createdAt', 'DESC')
            ->where('e.user = :userId')
            ->setParameter('userId', $userId);

        $paginator = new Paginator($queryBuilder, true);
        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);
        $total = $paginator->count();
        $lastPage = (int) ceil($total / $limit);

        return new PaginatorDTO(
            pagination: $paginator,
            total: $total,
            lastPage: $lastPage,
            page: $page,
        );
    }
}
