<?php

namespace cronv\Task\Management\Service;

use cronv\Task\Management\Trait\ServiceStoreTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base service
 */
abstract class BaseService
{
    use ServiceStoreTrait;

    /** @var int User ID */
    protected int $userId;
    /** @var int HTTP code */
    protected int $httpCode = Response::HTTP_OK;

    /**
     * Set User ID
     *
     * @param int $userId User ID
     * @return void
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Get User ID
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Set HTTP code
     *
     * @param int $httpCode HTTP code
     * @return void
     */
    protected function setHttpCode(int $httpCode): void
    {
        $this->httpCode = $httpCode;
    }

    /**
     * Get HTTP code
     *
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }
}