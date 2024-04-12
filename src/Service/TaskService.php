<?php

namespace cronv\Task\Management\Service;

use ArrayObject;
use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\DTO\DeleteTaskDTO;
use cronv\Task\Management\DTO\TaskDTO;
use cronv\Task\Management\DTO\TaskResponseDTO;
use cronv\Task\Management\DTO\UpdateTaskDTO;
use cronv\Task\Management\Entity\Task;
use cronv\Task\Management\Entity\User;
use cronv\Task\Management\Exception\StorageException;
use cronv\Task\Management\Trait\ServiceStoreTrait;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validation;

/**
 * Service Task
 */
class TaskService extends BaseService
{
    /**
     * TaskService constructor
     *
     * @param ManagerRegistry $em Contract covering object managers for a Doctrine
     */
    public function __construct(
        protected readonly ManagerRegistry $em,
    )
    {
    }

    /**
     * List task
     *
     * @param int $page Number page
     * @return PaginatorDTO
     */
    public function list(int $page): PaginatorDTO
    {
        $userId = $this->getUserId();
        return $this->em->getRepository(Task::class)->findPaginatedResults([
            'page' => $page,
            'userId' => $userId,
        ]);
    }

    /**
     * Add task
     *
     * @param TaskDTO $request TaskDTO data
     * @return object
     *
     * @throws StorageException
     */
    public function add(TaskDTO $request): object
    {
        if ($task = $this->findName($request->name)) {
            $this->setHttpCode(Response::HTTP_CONFLICT);
            return new TaskResponseDTO(
                message: null,
                errors: ['name' => 'Такой ресурс уже существует!']
            );
        }

        $userId = $this->getUserId();
        $user = $this->em->getRepository(User::class)->find($userId);

        $entityTask = new Task();
        $entityTask
            ->setUser($user)
            ->setName($request->name)
            ->setDescription($request->description);
        if ($request->deadline) {
            $entityTask->setDeadline(new DateTime($request->deadline));
        }

        $this->store($entityTask);
        $this->setHttpCode(Response::HTTP_CREATED);

        return new TaskResponseDTO(
            message: sprintf('Ресурс `%s` успешно создан.', $entityTask->getName()),
            errors: []
        );
    }

    /**
     * Update task
     *
     * @param UpdateTaskDTO $request UpdateTaskDTO data
     * @return object
     *
     * @throws StorageException
     */
    public function update(UpdateTaskDTO $request): object
    {
        $validator = Validation::createValidator();
        $validator->validate($request);

        if (!($task = $this->findName($request->name))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new TaskResponseDTO(
                message: null,
                errors: ['name' => 'Такого ресурса не существует!']
            );
        }

        if ($task?->getId() !== $request->uuid) {
            $this->setHttpCode(Response::HTTP_CONFLICT);
            return new TaskResponseDTO(
                message: null,
                errors: ['name' => 'Такая задача уже существует.']
            );
        }

        $entityTask = $task;
        $entityTask
            ->setName($request->name)
            ->setDescription($request->description);

        if ($request->deadline) {
            $entityTask->setDeadline(new DateTime($request->deadline));
        }

        $this->store($entityTask);
        $this->setHttpCode(Response::HTTP_CREATED);

        return new TaskResponseDTO(
            message: sprintf('Ресурс `%s` успешно обновлен.', $entityTask->getName()),
            errors: ['name' => 'Такая задача уже существует.']
        );
    }

    /**
     * Delete task
     *
     * @param DeleteTaskDTO $request DeleteTaskDTO data
     * @return object
     *
     * @throws StorageException
     */
    public function delete(DeleteTaskDTO $request): object
    {
        $validator = Validation::createValidator();
        $validator->validate($request);

        if (!($task = $this->em->getRepository(Task::class)->find($request->uuid))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new TaskResponseDTO(
                message: sprintf('Ресурса `%s` не существует.', $request->uuid),
                errors: []
            );
        }

        $this->deleteMultipleRecords($task);

        $this->setHttpCode(Response::HTTP_NO_CONTENT);
        return new TaskResponseDTO(
            message: 'Ресурс успешно удален.',
            errors: []
        );
    }

    /**
     * Search task by name
     *
     * @param string $name Name task
     * @return Task|null
     */
    protected function findName(string $name): ?Task
    {
        return $this->em->getRepository(Task::class)->IFindName($name);
    }
}