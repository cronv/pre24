<?php

namespace cronv\Task\Management\Repository\Transaction;

use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\Entity\Transaction\Transaction;
use cronv\Task\Management\Entity\Survey\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for transaction statistics.
 *
 * @extends ServiceEntityRepository<Transaction> Optional base class ServiceEntityRepository with a simplified constructor
 * (for auto-wiring).
 *
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null) Finds an entity by its primary key/identifier.
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null) Finds a single entity by a set of criteria.
 * @method Transaction[]    findAll() Finds all entities in the repository.
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) Finds entities
 * by a set of criteria.
 */
class TransactionRepository extends ServiceEntityRepository
{
    protected int $limit = 10;

    /**
     * {@inheritDoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
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
