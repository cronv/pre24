<?php

namespace cronv\Task\Management\Repository\Survey;

use cronv\Task\Management\Entity\Survey\SurveyMapping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for survey statistics information.
 *
 * @extends ServiceEntityRepository<SurveyMapping> Optional base class ServiceEntityRepository with a simplified constructor
 * (for auto-wiring).
 *
 * @method SurveyMapping|null find($id, $lockMode = null, $lockVersion = null) Finds an entity by its primary key/identifier.
 * @method SurveyMapping|null findOneBy(array $criteria, array $orderBy = null) Finds a single entity by a set of criteria.
 * @method SurveyMapping[]    findAll() Finds all entities in the repository.
 * @method SurveyMapping[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) Finds entities
 * by a set of criteria.
 */
class SurveyMappingRepository extends ServiceEntityRepository
{
    /**
     * {@inheritDoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SurveyMapping::class);
    }
}
