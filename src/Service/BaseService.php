<?php

namespace cronv\Task\Management\Service;

use cronv\Task\Management\Entity\User;
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

    /** @var string[] Roles User */
    protected array $roles = [];

    /**
     * Set User ID
     *
     * @param int $userId User ID
     * @return self
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
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

    /**
     * Set Roles User
     *
     * @param string[] $roles Roles
     * @return void
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Get Roles User
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Has role User by string
     *
     * @param string $role Role
     * @return bool
     */
    public function hasRoleUser(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Search survey by name
     *
     * @param string|array $name Name survey
     * @param string $persistent Entity
     *
     * @return ?object
     */
    protected function findName(string|array $name, string $persistent): ?object
    {
        return $this->em->getRepository($persistent)->IFindName($name);
    }

    /**
     * Get next.
     *
     * @param string $persistent Entity
     * @param array $params Params
     * @return object|null
     */
    protected function next(string $persistent, array $params): ?object
    {
        return $this->em->getRepository($persistent)->next($params);
    }

    /**
     * Get count question.
     *
     * @param string $persistent Entity
     * @param string $uuid UUID
     * @return int
     */
    protected function questionCount(string $persistent, string $uuid): int
    {
        return $this->em->getRepository($persistent)->questionCount($uuid);
    }

    /**
     * Get count question.
     *
     * @param string $persistent Entity
     * @param string $uuid UUID
     * @return ?string
     */
    protected function lastUuid(string $persistent, string $uuid): ?string
    {
        return $this->em->getRepository($persistent)->lastUuid($uuid);
    }

    /**
     * Get list question.
     *
     * @param string $persistent Entity
     * @param string $uuid UUID
     * @return ?array
     */
    public function question(string $persistent, string $uuid): ?array
    {
        return $this->em->getRepository($persistent)->question($uuid);
    }

    /**
     * Get count correct answers.
     *
     * @param string $persistent Entity
     * @param string $uuid UUID
     * @return ?int
     */
    public function correctCount(string $persistent, string $uuid): ?int
    {
        return $this->em->getRepository($persistent)->correctCount($uuid);
    }

    /**
     * Search survey by id (uuid)
     *
     * @param string|int $id ID
     * @param string $persistent Entity
     *
     * @return ?object
     */
    protected function find(string|int $id, string $persistent): ?object
    {
        return $this->em->getRepository($persistent)->find($id);
    }

    /**
     * Search survey by criteria
     *
     * @param array $criteria Criteria params
     * @param string $persistent Entity
     * @param ?array $orderBy Order By
     *
     * @return ?object
     */
    protected function findOneBy(string $persistent, array $criteria, ?array $orderBy = null): ?object
    {
        return $this->em->getRepository($persistent)->findOneBy($criteria, $orderBy);
    }

    /**
     * Search list by criteria
     *
     * @param array $criteria Criteria params
     * @param string $persistent Entity
     * @param ?array $orderBy Order By
     *
     * @return array
     */
    protected function findBy(string $persistent, array $criteria, ?array $orderBy = null): array
    {
        return $this->em->getRepository($persistent)->findBy($criteria, $orderBy);
    }

    /**
     * Get object User entity
     *
     * @return ?User
     */
    public function getUser(): ?User
    {
        $userId = $this->getUserId();
        return $this->em->getRepository(User::class)->find($userId);
    }

    /**
     * List user.
     *
     * @return array<User>
     */
    public function getUsers(): array
    {
        return $this->em->getRepository(User::class)->findAll();
    }
}
