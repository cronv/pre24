<?php

namespace cronv\Task\Management\Repository\Survey;

use cronv\Task\Management\Entity\Survey\Answer;
use cronv\Task\Management\Entity\Survey\Question;
use cronv\Task\Management\Entity\Survey\SurveyResults;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for survey results information.
 *
 * @extends ServiceEntityRepository<SurveyResults> Optional base class ServiceEntityRepository with a simplified constructor
 * (for auto-wiring).
 *
 * @method SurveyResults|null find($id, $lockMode = null, $lockVersion = null) Finds an entity by its primary key/identifier.
 * @method SurveyResults|null findOneBy(array $criteria, array $orderBy = null) Finds a single entity by a set of criteria.
 * @method SurveyResults[]    findAll() Finds all entities in the repository.
 * @method SurveyResults[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) Finds entities
 * by a set of criteria.
 */
class SurveyResultsRepository extends ServiceEntityRepository
{
    protected int $limit = 10;

    /**
     * {@inheritDoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SurveyResults::class);
    }

    /**
     * Get count correct answers (total).
     *
     * @param string $uuid UUID mapping
     * @return int|null
     */
    public function correctCount(string $uuid): ?int
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->select('COUNT(a.id) cnt')
            ->leftJoin(Answer::class, 'a', Join::WITH, 'e.answer = a.id')
            ->leftJoin(Question::class, 'q', Join::WITH, 'e.question = q.id')
            ->where('e.uuid = :uuid')
            ->andWhere('a.isCorrect = TRUE')
            ->orWhere('ILIKE(e.text, a.value) = TRUE')
            ->setParameter('uuid', $uuid);

        return $queryBuilder->getQuery()->getResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }
}
