<?php

namespace cronv\Task\Management\Service;

use ArrayObject;
use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\DTO\DeleteUuidDTO;
use cronv\Task\Management\DTO\TaskDTO;
use cronv\Task\Management\DTO\ResponseDTO;
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
        if ($task = $this->findName($request->name, Task::class)) {
            $this->setHttpCode(Response::HTTP_CONFLICT);
            return new ResponseDTO(
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

        return new ResponseDTO(
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

        if (!($task = $this->find($request->uuid, Task::class))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого ресурса не существует!']
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

        return new ResponseDTO(
            message: sprintf('Ресурс `%s` успешно обновлен.', $entityTask->getName()),
            errors: ['name' => 'Такая задача уже существует.']
        );
    }

    /**
     * Delete task
     *
     * @param DeleteUuidDTO $request DeleteUuidDTO data
     * @return object
     *
     * @throws StorageException
     */
    public function delete(DeleteUuidDTO $request): object
    {
        $validator = Validation::createValidator();
        $validator->validate($request);

        if (!($task = $this->em->getRepository(Task::class)->find($request->uuid))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: sprintf('Ресурса `%s` не существует.', $request->uuid),
                errors: []
            );
        }

        $this->deleteMultipleRecords($task);

        $this->setHttpCode(Response::HTTP_NO_CONTENT);
        return new ResponseDTO(
            message: 'Ресурс успешно удален.',
            errors: []
        );
    }
}