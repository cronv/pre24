<?php

namespace cronv\Task\Management\Repository\Expense;

use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\Entity\Expense\Expense;
use cronv\Task\Management\Entity\Expense\Income;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for income information.
 *
 * @extends ServiceEntityRepository<Income> Optional base class ServiceEntityRepository with a simplified constructor
 * (for auto-wiring).
 *
 * @method Income|null find($id, $lockMode = null, $lockVersion = null) Finds an entity by its primary key/identifier.
 * @method Income|null findOneBy(array $criteria, array $orderBy = null) Finds a single entity by a set of criteria.
 * @method Income[]    findAll() Finds all entities in the repository.
 * @method Income[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) Finds entities
 * by a set of criteria.
 */
class IncomeRepository extends ServiceEntityRepository
{
    /**
     * {@inheritDoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Income::class);
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

    /**
     * Get statistics.
     *
     * @return array|null
     */
    public function statistics(): ?array
    {
        $entityManager = $this->_em;

        // Doctrine query to fetch statistics from both incomes and expenses tables using createQuery
        $query = $entityManager->createQuery('
            SELECT 
                category,
                SUM(amount) AS total_amount
            FROM 
                ' . Expense::class . '
            GROUP BY 
                category
            UNION ALL
            SELECT 
                source AS category,
                SUM(amount) AS total_amount
            FROM 
                ' . Income::class . '
            GROUP BY 
                source
        ');

        return $query->getResult();
    }
}
