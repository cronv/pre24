<?php

namespace cronv\Task\Management\Repository\Survey;

use cronv\Task\Management\Entity\Survey\SurveyResults;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
}
