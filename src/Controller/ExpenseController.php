<?php

namespace cronv\Task\Management\Controller;

use cronv\Task\Management\Service\ExpenseService;
use cronv\Task\Management\Service\SurveyService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Controller expense
 */
class ExpenseController extends BaseController
{
    /**
     * ExpenseController constructor
     *
     * @param ExpenseService $expenseService Service expense
     */
    public function __construct(
        protected readonly ExpenseService         $expenseService,
        protected readonly UrlGeneratorInterface $urlGenerator
    )
    {
    }

    /**
     * Action info.
     *
     * @return Response
     */
    #[Route('/expense/index', name: 'ctb-expense-index', methods: ['GET', 'POST'])]
    public function info(): Response
    {
        $service = $this->expenseService;
        $service->setUserId($this->getUser()->getId());
        $object = $this->expenseService->info();
        return $this->render('@cronvTaskManagement/expense/index.html.twig', [
            'info' => $object,
        ]);
    }

    /**
     * Action expense.
     *
     * @param int $page Page
     * @return Response
     */
    #[Route('/expense/expenses/{page<\d+>}', name: 'ctb-expense', defaults: ['page' => 1], methods: ['GET', 'POST'])]
    public function expense(int $page): Response
    {
        $service = $this->expenseService;
        $service->setUserId($this->getUser()->getId());
        $object = $this->expenseService->listExpense($page);
        return $this->render('@cronvTaskManagement/expense/index.html.twig', [
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
    #[Route('/expense/incomes/{page<\d+>}', name: 'ctb-expense-income', defaults: ['page' => 1], methods: ['GET', 'POST'])]
    public function income(int $page): Response
    {
        $service = $this->expenseService;
        $service->setUserId($this->getUser()->getId());
        $object = $this->expenseService->listIncome($page);
        return $this->render('@cronvTaskManagement/expense/index.html.twig', [
            'pagination' => $object->pagination,
            'paginator_render' => $this->renderView('@cronvTaskManagement/paginator/paginator.html.twig', [
                'total' => $object->total,
                'lastPage' => $object->lastPage,
                'page' => $object->page,
            ]),
        ]);
    }
}
