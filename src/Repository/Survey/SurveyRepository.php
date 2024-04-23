<?php

namespace cronv\Task\Management\Repository\Survey;

use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\Entity\Survey\Question;
use cronv\Task\Management\Entity\Survey\Survey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for survey information.
 *
 * @extends ServiceEntityRepository<Survey> Optional base class ServiceEntityRepository with a simplified constructor
 * (for auto-wiring).
 *
 * @method Survey|null find($id, $lockMode = null, $lockVersion = null) Finds an entity by its primary key/identifier.
 * @method Survey|null findOneBy(array $criteria, array $orderBy = null) Finds a single entity by a set of criteria.
 * @method Survey[]    findAll() Finds all entities in the repository.
 * @method Survey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) Finds entities
 * by a set of criteria.
 */
class SurveyRepository extends ServiceEntityRepository
{
    protected int $limit = 10;

    /**
     * {@inheritDoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Survey::class);
    }

    /**
     * Search surveys (paginator)
     *
     * @param array $params Params
     * @return PaginatorDTO
     */
    public function findPaginatedResults(array $params): PaginatorDTO
    {
        $page = $params['page'];
        $limit = $this->limit;

        $queryBuilder = $this->createQueryBuilder('e')
            ->select('e, COUNT(q.id) questionCount')
            ->leftJoin(Question::class, 'q', Join::WITH, 'q.survey = e.uuid')
            ->orderBy('e.createdAt', 'DESC')
            ->groupBy('e.uuid');

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
     * Next survey.
     *
     * @param array $params Params
     * @return PaginatorDTO
     */
    public function next(array $params): PaginatorDTO
    {
        $uuid = $params['uuid'];
        $page = $params['page'];
        $limit = 1;

        $queryBuilder = $this->createQueryBuilder('e')
            ->select('e, q.name, q.id q_id')
            ->leftJoin(Question::class, 'q', Join::WITH, 'q.survey = e.uuid')
            ->where('e.uuid = :uuid')
            ->setParameter('uuid', $uuid);

        $paginator = new Paginator($queryBuilder, true);
        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
        ;

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
     * @return ?Survey
     *
     * @throws NonUniqueResultException
     */
    public function IFindName(string $name): ?Survey
    {
        return $this->createQueryBuilder('e')
            ->where('ILIKE(e.name, :name) = TRUE')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
