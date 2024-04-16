<?php

namespace cronv\Task\Management\DTO;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Task Response DTO
 */
readonly class ResponseDTO
{
    /**
     * ResponseDTO constructor.
     *
     * @param ?string $message Message
     * @param array $errors Array errors
     */
    public function __construct(
        public ?string $message,
        public array $errors
    )
    {
    }
}
