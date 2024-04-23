<?php

namespace cronv\Task\Management\Repository\Survey;

use cronv\Task\Management\Entity\Survey\QuestionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for question type information.
 *
 * @extends ServiceEntityRepository<QuestionType> Optional base class ServiceEntityRepository with a simplified constructor
 * (for auto-wiring).
 *
 * @method QuestionType|null find($id, $lockMode = null, $lockVersion = null) Finds an entity by its primary key/identifier.
 * @method QuestionType|null findOneBy(array $criteria, array $orderBy = null) Finds a single entity by a set of criteria.
 * @method QuestionType[]    findAll() Finds all entities in the repository.
 * @method QuestionType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) Finds entities
 * by a set of criteria.
 */
class QuestionTypeRepository extends ServiceEntityRepository
{
    protected int $limit = 10;

    /**
     * {@inheritDoc}
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionType::class);
    }

    /**
     * Find ILIKE (PostgreSQL)
     *
     * @param string $name Name task
     * @return ?QuestionType
     *
     * @throws NonUniqueResultException
     */
    public function IFindName(string $name): ?QuestionType
    {
        return $this->createQueryBuilder('e')
            ->where('ILIKE(e.name, :name) = TRUE')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
