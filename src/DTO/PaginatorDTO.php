<?php

namespace cronv\Task\Management\DTO;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Paginator DTO
 */
readonly class PaginatorDTO
{
    /**
     * PaginatorDTO constructor.
     *
     * @param Paginator $pagination Entity
     * @param int $total Общее количество
     * @param int $lastPage Последняя страница
     * @param int $page Текущая страница
     */
    public function __construct(
        public Paginator $pagination,
        public int       $total,
        public int       $lastPage,
        public int       $page,
    )
    {
    }
}
