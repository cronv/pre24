<?php

namespace cronv\Task\Management\Service;

use cronv\Task\Management\DTO\DeleteIdDTO;
use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\DTO\ResponseDTO;
use cronv\Task\Management\DTO\Survey\ParamsDTO;
use cronv\Task\Management\DTO\Transaction\TransactionDTO;
use cronv\Task\Management\DTO\Transaction\TransactionUpdateDTO;
use cronv\Task\Management\DTO\Transaction\TypeDTO;
use cronv\Task\Management\DTO\Transaction\TypeUpdateDTO;
use cronv\Task\Management\Entity\Transaction\Transaction;
use cronv\Task\Management\Entity\Transaction\TransactionType;
use cronv\Task\Management\Entity\User;
use cronv\Task\Management\Exception\StorageException;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validation;

/**
 * Service expense
 */
class TransactionService extends BaseService
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
     * Get list transaction by User.
     *
     * @param int $page Page
     * @return PaginatorDTO
     */
    public function listTransaction(int $page): PaginatorDTO
    {
        return $this->em->getRepository(Transaction::class)->findPaginatedResults([
            'page' => $page,
            'userId' => $this->getUserId(),
        ]);
    }

    /**
     * Get list transaction type.
     *
     * @param int $page Page
     * @return PaginatorDTO
     */
    public function listTransactionType(int $page): PaginatorDTO
    {
        return $this->em->getRepository(TransactionType::class)->findPaginatedResults([
            'page' => $page,
            'userId' => $this->getUserId(),
        ]);
    }

    /**
     * Add transaction.
     *
     * @param TransactionDTO $request DTO
     * @return ResponseDTO
     * @throws StorageException
     */
    public function addTransaction(TransactionDTO $request): ResponseDTO
    {
        if (!($entityUser = $this->find($request->userId, User::class))) {
            $this->setHttpCode(Response::HTTP_CONFLICT);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого пользователя не существует!']
            );
        }

        if (!($entityType = $this->find($request->typeId, TransactionType::class))) {
            $this->setHttpCode(Response::HTTP_CONFLICT);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого типа не существует!']
            );
        }

        $entityTransaction = new Transaction($entityUser);
        $entityTransaction->setType($entityType)
            ->setAmount($request->amount)
            ->setDescription($request->description)
            ->setDatedAt(new DateTime($request->datedAt));

        $this->store($entityTransaction);
        $this->setHttpCode(Response::HTTP_CREATED);

        return new ResponseDTO(
            message: sprintf('Ресурс `%s` успешно создан.', $entityTransaction->getType()->getName()),
            errors: []
        );
    }

    /**
     * Update transaction.
     *
     * @param TransactionUpdateDTO $request DTO
     * @return ResponseDTO
     * @throws StorageException
     */
    public function updateTransaction(TransactionUpdateDTO $request): ResponseDTO
    {
        $validator = Validation::createValidator();
        $validator->validate($request);

        if (!($entityTransaction = $this->find($request->id, Transaction::class))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого ресурса не существует!']
            );
        }

        if (!($entityType = $this->find($request->typeId, TransactionType::class))) {
            $this->setHttpCode(Response::HTTP_CONFLICT);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого типа не существует!']
            );
        }

        $entityTransaction->setType($entityType)
            ->setAmount($request->amount)
            ->setDescription($request->description)
            ->setDatedAt(new DateTime($request->datedAt));

        $this->store($entityTransaction);
        $this->setHttpCode(Response::HTTP_OK);

        return new ResponseDTO(
            message: sprintf('Ресурс `%s` успешно обновлен.', $entityTransaction->getId()),
            errors: []
        );
    }

    /**
     * Delete transaction.
     *
     * @param DeleteIdDTO $request
     * @return ResponseDTO
     * @throws StorageException
     */
    public function deleteTransaction(DeleteIdDTO $request): ResponseDTO
    {
        $validator = Validation::createValidator();
        $validator->validate($request);

        if (!($entityTransaction = $this->em->getRepository(Transaction::class)->find($request->id))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: sprintf('Ресурса `%s` не существует.', $request->id),
                errors: []
            );
        }

        $this->deleteMultipleRecords($entityTransaction);

        $this->setHttpCode(Response::HTTP_NO_CONTENT);
        return new ResponseDTO(
            message: 'Ресурс успешно удален.',
            errors: []
        );
    }

    /**
     * Add transaction type.
     *
     * @param TypeDTO $request DTO
     * @return ResponseDTO
     * @throws StorageException
     */
    public function addTransactionType(TypeDTO $request): ResponseDTO
    {
        if ($entityType = $this->findName($request->name, TransactionType::class)) {
            $this->setHttpCode(Response::HTTP_CONFLICT);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такой ресурс уже существует!']
            );
        }

        $entityType = new TransactionType();
        $entityType->setName($request->name);

        $this->store($entityType);
        $this->setHttpCode(Response::HTTP_CREATED);

        return new ResponseDTO(
            message: sprintf('Ресурс `%s` успешно создан.', $entityType->getName()),
            errors: []
        );
    }

    /**
     * Update transaction type.
     *
     * @param TypeUpdateDTO $request DTO
     * @return ResponseDTO
     * @throws StorageException
     */
    public function updateTransactionType(TypeUpdateDTO $request): ResponseDTO
    {
        $validator = Validation::createValidator();
        $validator->validate($request);

        if (!($entityType = $this->find($request->id, TransactionType::class))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого ресурса не существует!']
            );
        }

        $entityType->setName($request->name);

        $this->store($entityType);
        $this->setHttpCode(Response::HTTP_OK);

        return new ResponseDTO(
            message: sprintf('Ресурс `%s` успешно обновлен.', $entityType->getName()),
            errors: []
        );
    }

    /**
     * Delete transaction type and transactions operation.
     *
     * @param DeleteIdDTO $request
     * @return ResponseDTO
     * @throws StorageException
     */
    public function deleteTransactionType(DeleteIdDTO $request): ResponseDTO
    {
        $validator = Validation::createValidator();
        $validator->validate($request);

        if (!($entityType = $this->em->getRepository(TransactionType::class)->find($request->id))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: sprintf('Ресурса `%s` не существует.', $request->id),
                errors: []
            );
        }

        $transactions = $this->em->getRepository(Transaction::class)->findBy(['type' => $request->id]);

        $this->deleteMultipleRecords($transactions, $entityType);

        $this->setHttpCode(Response::HTTP_NO_CONTENT);
        return new ResponseDTO(
            message: 'Ресурс успешно удален.',
            errors: []
        );
    }

    /**
     * List type.
     *
     * @return array<TransactionType>
     */
    public function getTypes(): array
    {
        return $this->em->getRepository(TransactionType::class)->findAll();
    }
}
