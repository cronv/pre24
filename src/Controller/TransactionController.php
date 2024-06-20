<?php

namespace cronv\Task\Management\Controller;

use cronv\Task\Management\DTO\DeleteIdDTO;
use cronv\Task\Management\DTO\Transaction\TransactionDTO;
use cronv\Task\Management\DTO\Transaction\TransactionUpdateDTO;
use cronv\Task\Management\DTO\Transaction\TypeDTO;
use cronv\Task\Management\DTO\Transaction\TypeUpdateDTO;
use cronv\Task\Management\Exception\StorageException;
use cronv\Task\Management\Service\TransactionService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Controller expense
 */
class TransactionController extends BaseController
{
    /**
     * ExpenseController constructor
     *
     * @param TransactionService $transactionService Service expense
     */
    public function __construct(
        protected readonly TransactionService    $transactionService,
        protected readonly UrlGeneratorInterface $urlGenerator
    )
    {
    }

    /**
     * Action info.
     *
     * @return Response
     */
    #[Route('/transaction/info', name: 'ctb-transaction-index', methods: ['GET', 'POST'])]
    public function info(): Response
    {
        $service = $this->transactionService;
        $service->setUserId($this->getUser()->getId());
        $object = $this->transactionService->info();
        return $this->render('@cronvTaskManagement/transaction/index.html.twig', [
            'info' => $object,
        ]);
    }

    /**
     * Action expense.
     *
     * @param int $page Page
     * @return Response
     */
    #[Route('/transaction/{page<\d+>}', name: 'ctb-transaction', defaults: ['page' => 1], methods: ['GET', 'POST'])]
    public function transaction(int $page): Response
    {
        $service = $this->transactionService;
        $service->setUserId($this->getUser()->getId());
        $object = $this->transactionService->listTransaction($page);
        return $this->render('@cronvTaskManagement/transaction/transaction.html.twig', [
            'listUsers' => $service->getUsers(),
            'listType' => $service->getTypes(),
            'pagination' => $object->pagination,
            'paginator_render' => $this->renderView('@cronvTaskManagement/paginator/paginator.html.twig', [
                'total' => $object->total,
                'lastPage' => $object->lastPage,
                'page' => $object->page,
            ]),
        ]);
    }

    /**
     * Action income.
     *
     * @param int $page Page
     * @return Response
     */
    #[Route('/transaction/type/{page<\d+>}', name: 'ctb-transaction-type', defaults: ['page' => 1], methods: ['GET', 'POST'])]
    public function transactionType(int $page): Response
    {
        $service = $this->transactionService;
        $service->setUserId($this->getUser()->getId());
        $object = $this->transactionService->listTransactionType($page);
        return $this->render('@cronvTaskManagement/transaction/transaction_type.html.twig', [
            'pagination' => $object->pagination,
            'paginator_render' => $this->renderView('@cronvTaskManagement/paginator/paginator.html.twig', [
                'total' => $object->total,
                'lastPage' => $object->lastPage,
                'page' => $object->page,
            ]),
        ]);
    }

    /**
     * Action add transaction.
     *
     * @param TransactionDTO $request DTO
     * @return Response
     *
     * @throws StorageException
     */
    #[Route(path: '/transaction/add', name: 'ctb-transaction-add')]
    public function addTransaction(TransactionDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $service = $this->transactionService;
        $data = $service->addTransaction($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action update transaction.
     *
     * @param TransactionUpdateDTO $request DTO
     * @return Response
     *
     * @throws StorageException
     */
    #[Route('/transaction/update/{id<\d+>}', name: 'ctb-transaction-update', methods: 'PUT')]
    public function updateTransaction(TransactionUpdateDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->transactionService;
        $service->setUserId($this->getUser()->getId());
        $data = $service->updateTransaction($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action delete transaction.
     *
     * @param DeleteIdDTO $request DTO
     * @return Response
     * @throws StorageException
     */
    #[Route('/transaction/delete/{id<\d+>}', name: 'ctb-transaction-delete', methods: 'DELETE')]
    public function deleteTransaction(DeleteIdDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->transactionService;
        $service->setUserId($this->getUser()->getId());
        $data = $service->deleteTransaction($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action add transaction type.
     *
     * @param TypeDTO $request DTO
     * @return Response
     *
     * @throws StorageException
     */
    #[Route(path: '/transaction/type/add', name: 'ctb-transaction-type-add')]
    public function addTransactionType(TypeDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $service = $this->transactionService;
        $data = $service->addTransactionType($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action update transaction type.
     *
     * @param TypeUpdateDTO $request DTO
     * @return Response
     *
     * @throws StorageException
     */
    #[Route('/transaction/type/update/{id<\d+>}', name: 'ctb-transaction-type-update', methods: 'PUT')]
    public function updateTransactionType(TypeUpdateDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->transactionService;
        $service->setUserId($this->getUser()->getId());
        $data = $service->updateTransactionType($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action delete transaction type.
     *
     * @param DeleteIdDTO $request DTO
     * @return Response
     * @throws StorageException
     */
    #[Route('/transaction/type/delete/{id<\d+>}', name: 'ctb-transaction-type-delete', methods: 'DELETE')]
    public function deleteTransactionType(DeleteIdDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->transactionService;
        $service->setUserId($this->getUser()->getId());
        $data = $service->deleteTransactionType($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }
}
