<?php

namespace cronv\Task\Management\Repository\Survey;

use cronv\Task\Management\Entity\Survey\SurveyStatistics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for survey statistics information.
 *
 * @extends ServiceEntityRepository<SurveyStatistics> Optional base class ServiceEntityRepository with a simplified constructor
 * (for auto-wiring).
 *
 * @method SurveyStatistics|null find($id, $lockMode = null, $lockVersion = null) Finds an entity by its primary key/identifier.
 * @method SurveyStatistics|null findOneBy(array $criteria, array $orderBy = null) Finds a single entity by a set of criteria.
 * @method SurveyStatistics[]    findAll() Finds all entities in the repository.
 * @method SurveyStatistics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) Finds entities
 * by a set of criteria.
 */
class SurveyStatisticsRepository extends ServiceEntityRepository
{
    /**
     * {@inheritDoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SurveyStatistics::class);
    }
}
