<?php

namespace cronv\Task\Management\Repository\Survey;

use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\DTO\Survey\ParamsDTO;
use cronv\Task\Management\Entity\Survey\Answer;
use cronv\Task\Management\Entity\Survey\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for question information.
 *
 * @extends ServiceEntityRepository<Question> Optional base class ServiceEntityRepository with a simplified constructor
 * (for auto-wiring).
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null) Finds an entity by its primary key/identifier.
 * @method Question|null findOneBy(array $criteria, array $orderBy = null) Finds a single entity by a set of criteria.
 * @method Question[]    findAll() Finds all entities in the repository.
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) Finds entities
 * by a set of criteria.
 */
class QuestionRepository extends ServiceEntityRepository
{
    protected int $limit = 10;

    /**
     * {@inheritDoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * Search questions (paginator)
     *
     * @param array $params Params
     * @return PaginatorDTO
     */
    public function findPaginatedResults(array $params): PaginatorDTO
    {
        $id = $params['uuid'];
        $page = $params['page'];
        $limit = $this->limit;

        $queryBuilder = $this->createQueryBuilder('e')
            ->select('e, COUNT(a.id) questionCount')
            ->leftJoin(Answer::class, 'a', Join::WITH, 'a.question = e.id')
            ->where('e.survey = :survey_uuid')
            ->setParameter('survey_uuid', $id)
            ->orderBy('e.createdAt', 'DESC')
            ->groupBy('e.id');

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
     * @return ?Question
     *
     * @throws NonUniqueResultException
     */
    public function IFindName(array $params): ?Question
    {
        list($uuid, $name) = $params;
        return $this->createQueryBuilder('e')
            ->where('ILIKE(e.name, :name) = TRUE')
            ->andWhere('e.survey = :uuid')
            ->setParameter('name', $name)
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get answers or by answers ids.
     *
     * @param array $params Params DTO
     * @return array|null
     */
    public function getAnswers(array $params): ?array
    {
        $uuid = $params['uuid'];
        $id = $params['id'];
        $ids = [];

        if (isset($params['ids']) && $params['ids']) {
            $ids = $params['ids'];
        }

        $queryBuilder = $this->createQueryBuilder('e')
            ->select('a')
            ->leftJoin(Answer::class, 'a', Join::WITH, 'a.question = e.id')
            ->where('e.survey = :survey_uuid')
            ->andWhere('e.id = :question_id')
            ->setParameter('survey_uuid', $uuid)
            ->setParameter('question_id', $id);

        if ($ids) {
            $queryBuilder->andWhere('a.id IN (:ids)')
                ->setParameter('ids', $ids);
        }

        $queryBuilder->orderBy('e.createdAt', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }
}
