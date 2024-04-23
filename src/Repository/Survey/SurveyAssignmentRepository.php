<?php

namespace cronv\Task\Management\Repository\Survey;

use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\Entity\Survey\SurveyAssignment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for survey assignment information.
 *
 * @extends ServiceEntityRepository<SurveyAssignment> Optional base class ServiceEntityRepository with a simplified constructor
 * (for auto-wiring).
 *
 * @method SurveyAssignment|null find($id, $lockMode = null, $lockVersion = null) Finds an entity by its primary key/identifier.
 * @method SurveyAssignment|null findOneBy(array $criteria, array $orderBy = null) Finds a single entity by a set of criteria.
 * @method SurveyAssignment[]    findAll() Finds all entities in the repository.
 * @method SurveyAssignment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) Finds entities
 * by a set of criteria.
 */
class SurveyAssignmentRepository extends ServiceEntityRepository
{
    protected int $limit = 10;

    /**
     * {@inheritDoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SurveyAssignment::class);
    }

    /**
     * Search assignment (paginator)
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
            ->select('e');

        if ($userId) {
            $queryBuilder->where('(e.access IS NULL OR e.access = TRUE)')
                ->andWhere('e.user = :userId')
                ->setParameter('userId', $userId);
        }

        $queryBuilder->orderBy('e.startedAt', 'DESC');

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
     * @param array $params Params
     * @return ?SurveyAssignment
     *
     * @throws NonUniqueResultException
     */
    public function IFindName(array $params): ?SurveyAssignment
    {
        list($userId, $surveyId) = $params;
        return $this->createQueryBuilder('e')
            ->where('e.user = :userId')
            ->andWhere('e.survey = :surveyId')
            ->setParameter('userId', $userId)
            ->setParameter('surveyId', $surveyId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
