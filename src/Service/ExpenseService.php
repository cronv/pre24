<?php

namespace cronv\Task\Management\Service;

use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\DTO\Survey\ParamsDTO;
use cronv\Task\Management\Entity\Expense\Expense;
use cronv\Task\Management\Entity\Expense\Income;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Service expense
 */
class ExpenseService extends BaseService
{
    /**
     * SurveyService constructor
     *
     * @param ManagerRegistry $em Contract covering object managers for a Doctrine
     */
    public function __construct(
        protected readonly ManagerRegistry $em,
    )
    {
    }

    /**
     * Get info.
     *
     * @return object
     */
    public function info(): object
    {
        $std = new \stdClass();
        $std->totalExpense = 20;
        $std->totalIncome = 20;

        return $std;
    }

    /**
     * Get list expense by User.
     *
     * @param int $page Page
     * @return PaginatorDTO
     */
    public function listExpense(int $page): PaginatorDTO
    {
        return $this->em->getRepository(Expense::class)->findPaginatedResults([
            'page' => $page,
            'userId' => $this->getUserId(),
        ]);
    }

    /**
     * Get list expense by User.
     *
     * @param int $page Page
     * @return PaginatorDTO
     */
    public function listIncome(int $page): PaginatorDTO
    {
        return $this->em->getRepository(Income::class)->findPaginatedResults([
            'page' => $page,
            'userId' => $this->getUserId(),
        ]);
    }
}
