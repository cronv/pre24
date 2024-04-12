<?php

namespace cronv\Task\Management\Repository;

use ArrayObject;
use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for task information.
 *
 * @extends ServiceEntityRepository<Task> Optional base class ServiceEntityRepository with a simplified constructor
 * (for auto-wiring).
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null) Finds an entity by its primary key/identifier.
 * @method Task|null findOneBy(array $criteria, array $orderBy = null) Finds a single entity by a set of criteria.
 * @method Task[]    findAll() Finds all entities in the repository.
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) Finds entities
 * by a set of criteria.
 */
class TaskRepository extends ServiceEntityRepository
{
    protected int $limit = 10;
    /**
     * {@inheritDoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Search tasks (paginator)
     *
     * @param array $params Params
     * @return PaginatorDTO
     */
    public function findPaginatedResults(array $params): PaginatorDTO
    {
        $userId = $params['userId'];
        $page = $params['page'];
        $limit = $this->limit;

        $queryBuilder = $this->createQueryBuilder('e')
            ->where('e.user = :user')
            ->setParameter('user', $userId)
            ->orderBy('e.createdAt', 'DESC')
        ;

        $paginator = new Paginator($queryBuilder);
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
     * @return Task|null
     *
     * @throws NonUniqueResultException
     */
    public function IFindName(string $name): ?Task
    {
        return $this->createQueryBuilder('e')
            ->where('ILIKE(e.name, :name) = TRUE')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
