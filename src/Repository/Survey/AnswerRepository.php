<?php

namespace cronv\Task\Management\Repository\Survey;

use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\Entity\Survey\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for answer information.
 *
 * @extends ServiceEntityRepository<Answer> Optional base class ServiceEntityRepository with a simplified constructor
 * (for auto-wiring).
 *
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null) Finds an entity by its primary key/identifier.
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null) Finds a single entity by a set of criteria.
 * @method Answer[]    findAll() Finds all entities in the repository.
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) Finds entities
 * by a set of criteria.
 */
class AnswerRepository extends ServiceEntityRepository
{
    protected int $limit = 10;

    /**
     * {@inheritDoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    /**
     * Search answers (paginator)
     *
     * @param array $params Params
     * @return PaginatorDTO
     */
    public function findPaginatedResults(array $params): PaginatorDTO
    {
        $id = $params['question_id'];
        $page = $params['page'];
        $limit = $this->limit;

        $queryBuilder = $this->createQueryBuilder('e')
            ->where('e.question = :question_id')
            ->setParameter('question_id', $id)
            ->orderBy('e.id', 'DESC');

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
     * @return ?Answer
     *
     * @throws NonUniqueResultException
     */
    public function IFindName(array $params): ?Answer
    {
        list($questionId, $name) = $params;
        return $this->createQueryBuilder('e')
            ->where('ILIKE(e.value, :value) = TRUE')
            ->andWhere('e.question = :id')
            ->setParameter('value', $name)
            ->setParameter('id', $questionId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
