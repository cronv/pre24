<?php

namespace cronv\Task\Management\Repository\Transaction;

use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\Entity\Transaction\TransactionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for transaction type.
 *
 * @extends ServiceEntityRepository<TransactionType> Optional base class ServiceEntityRepository with a simplified constructor
 * (for auto-wiring).
 *
 * @method TransactionType|null find($id, $lockMode = null, $lockVersion = null) Finds an entity by its primary key/identifier.
 * @method TransactionType|null findOneBy(array $criteria, array $orderBy = null) Finds a single entity by a set of criteria.
 * @method TransactionType[]    findAll() Finds all entities in the repository.
 * @method TransactionType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) Finds entities
 * by a set of criteria.
 */
class TransactionTypeRepository extends ServiceEntityRepository
{
    protected int $limit = 10;

    /**
     * {@inheritDoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionType::class);
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

        $queryBuilder = $this->createQueryBuilder('e')->select('e')->orderBy('e.id', 'DESC');

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

    /**
     * Find ILIKE (PostgreSQL)
     *
     * @param string $name Name task
     * @return ?TransactionType
     *
     * @throws NonUniqueResultException
     */
    public function IFindName(string $name): ?TransactionType
    {
        return $this->createQueryBuilder('e')
            ->where('ILIKE(e.name, :name) = TRUE')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
