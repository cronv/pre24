<?php

namespace cronv\Task\Management\Controller;

use cronv\Task\Management\DTO\DeleteIdDTO;
use cronv\Task\Management\DTO\DeleteUuidDTO;
use cronv\Task\Management\DTO\Survey\AnswerAddDTO;
use cronv\Task\Management\DTO\Survey\AnswerUpdateDTO;
use cronv\Task\Management\DTO\Survey\AssignmentDTO;
use cronv\Task\Management\DTO\Survey\AssignmentUpdateDTO;
use cronv\Task\Management\DTO\Survey\DeleteAssigmentDTO;
use cronv\Task\Management\DTO\Survey\ParamsDTO;
use cronv\Task\Management\DTO\Survey\ProcessedDTO;
use cronv\Task\Management\DTO\Survey\QuestionAddDTO;
use cronv\Task\Management\DTO\Survey\QuestionUpdateDTO;
use cronv\Task\Management\DTO\Survey\SurveyDTO;
use cronv\Task\Management\DTO\Survey\UpdateSurveyDTO;
use cronv\Task\Management\Exception\StorageException;
use cronv\Task\Management\Service\SurveyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Controller survey
 */
class SurveyController extends BaseController
{
    /**
     * SurveyController constructor
     *
     * @param SurveyService $surveyService Service Survey
     */
    public function __construct(
        protected readonly SurveyService $surveyService,
        protected readonly UrlGeneratorInterface $urlGenerator
    )
    {
    }

    /**
     * Action list survey
     *
     * @param int $page Number page
     * @return Response
     */
    #[Route(path: '/survey/admin/{page<\d+>}', name: 'ctb-survey-admin', defaults: ['page' => 1], methods: ['GET', 'POST'])]
    public function adminSurvey(int $page): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $this->surveyService->setUserId($this->getUser()->getId())
            ->setRoles($this->getUser()->getRoles());

        $object = $this->surveyService->listSurvey($page);

        return $this->render('@cronvTaskManagement/survey/admin/index.html.twig', [
            'pagination' => $object->pagination,
            'paginator_render' => $this->renderView('@cronvTaskManagement/paginator/paginator.html.twig', [
                'total' => $object->total,
                'lastPage' => $object->lastPage,
                'page' => $object->page,
            ]),
        ]);
    }

    /**
     * Action list question
     *
     * @param ParamsDTO $request ListParams DTO
     * @return Response
     */
    #[Route(path: '/survey/admin/{uuid<\S+>}/question/{page<\d+>}', name: 'ctb-survey-admin-q', defaults: ['page' => 1],
        methods: ['GET', 'POST'])]
    public function adminQuestion(ParamsDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $service->setUserId($this->getUser()->getId())
            ->setRoles($this->getUser()->getRoles());

        $object = $service->listQuestion($request);

        return $this->render('@cronvTaskManagement/survey/admin/question.html.twig', [
            'questionTypes' => $service->getQuestionTypes(),
            'hiddenInputs' => [
                'uuid' => $request->uuid,
            ],
            'pagination' => $object->pagination,
            'paginator_render' => $this->renderView('@cronvTaskManagement/paginator/paginator.html.twig', [
                'total' => $object->total,
                'lastPage' => $object->lastPage,
                'page' => $object->page,
            ]),
        ]);
    }

    /**
     * Action list answer
     *
     * @param ParamsDTO $request ListParams DTO
     * @return Response
     */
    #[Route(path: '/survey/admin/{uuid<\S+>}/question/answer/{id<\d+>}/{page<\d+>}', name: 'ctb-survey-admin-a', defaults: ['page' => 1],
        methods: ['GET', 'POST'])]
    public function adminAnswer(ParamsDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $service->setUserId($this->getUser()->getId())
            ->setRoles($this->getUser()->getRoles());

        $object = $service->listAnswer($request);

        return $this->render('@cronvTaskManagement/survey/admin/answer.html.twig', [
            'listCorrects' => $service->getCorrects(),
            'hiddenInputs' => [
                'id' => $request->id,
                'uuid' => $request->uuid,
            ],
            'pagination' => $object->pagination,
            'paginator_render' => $this->renderView('@cronvTaskManagement/paginator/paginator.html.twig', [
                'total' => $object->total,
                'lastPage' => $object->lastPage,
                'page' => $object->page,
            ]),
        ]);
    }

    /**
     * Action list assigment
     *
     * @param ParamsDTO $request ListParams DTO
     * @return Response
     */
    #[Route(path: '/survey/admin/assignment/{page<\d+>}', name: 'ctb-survey-admin-am', defaults: ['page' => 1],
        methods: ['GET', 'POST'])]
    public function adminAssignment(ParamsDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $service->setUserId($this->getUser()->getId())
            ->setRoles($this->getUser()->getRoles());

        $object = $service->listAssignment($request);

        return $this->render('@cronvTaskManagement/survey/admin/assignment.html.twig', [
            'listCorrects' => $service->getCorrects(),
            'listUsers' => $service->getUsers(),
            'listSurvey' => $service->getSurvey(),
            'pagination' => $object->pagination,
            'paginator_render' => $this->renderView('@cronvTaskManagement/paginator/paginator.html.twig', [
                'total' => $object->total,
                'lastPage' => $object->lastPage,
                'page' => $object->page,
            ]),
        ]);
    }

    /**
     * Action list survey
     *
     * @param ParamsDTO $request ListParams DTO
     * @return Response
     */
    #[Route(path: '/survey/{page<\d+>}', name: 'ctb-survey', defaults: ['page' => 1],
        methods: ['GET', 'POST'])]
    public function survey(ParamsDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $service = $this->surveyService;
        $request->userId = $this->getUser()?->getId();

        $object = $service->listAssignment($request);

        return $this->render('@cronvTaskManagement/survey/index.html.twig', [
            'listCorrects' => $service->getCorrects(),
            'listUsers' => $service->getUsers(),
            'listSurvey' => $service->getSurvey(),
            'pagination' => $object->pagination,
            'paginator_render' => $this->renderView('@cronvTaskManagement/paginator/paginator.html.twig', [
                'total' => $object->total,
                'lastPage' => $object->lastPage,
                'page' => $object->page,
            ]),
        ]);
    }

    /**
     * Action survey
     *
     * @param ParamsDTO $request ListParams DTO
     * @return Response
     */
    #[Route(path: '/survey/attempts/{uuid}', name: 'ctb-survey-as', methods: ['GET', 'POST'])]
    public function attempts(ParamsDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $service = $this->surveyService;
        $request->userId = $this->getUser()?->getId();

        $object = $service->infoAttempts($request);

        return $this->render('@cronvTaskManagement/survey/attempts.html.twig', [
            'attempts' => $object,
        ]);
    }

    /**
     * Action processed survey
     *
     * @param ProcessedDTO $request DTO
     * @return Response
     */
    #[Route(path: '/survey/processed/{uuid}/{page<\d+>}', name: 'ctb-survey-processed', defaults: ['page' => 1],
        methods: ['GET', 'POST'])]
    public function processedSurvey(ProcessedDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $service = $this->surveyService;
        $service->setUserId($this->getUser()->getId())
            ->setRoles($this->getUser()->getRoles());
        $request->userId = $this->getUser()?->getId();
        $object = $service->processed($request);

        // redirect attempts action
        if ($object->final) {
            $url = $this->generateUrl('ctb-survey-as', ['uuid' => $request->uuid]);
            return new RedirectResponse($url);
        }

        return $this->render('@cronvTaskManagement/survey/processed.html.twig', [
            'processed' => $object,
        ]);
    }

    /**
     * Action statistics survey
     *
     * @return Response
     */
    #[Route(path: '/survey/statistics', name: 'ctb-survey-statistics', methods: ['GET', 'POST'])]
    public function statistics(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $service->setUserId($this->getUser()->getId())
            ->setRoles($this->getUser()->getRoles());
        $statistics = $service->statistics();

        return $this->render('@cronvTaskManagement/survey/statistics.html.twig', [
            'statistics' => $statistics,
        ]);
    }

    /**
     * Action add survey
     *
     * @param SurveyDTO $request DTO
     * @return Response
     *
     * @throws StorageException
     */
    #[Route(path: '/survey/add', name: 'ctb-survey-admin-add')]
    public function addSurvey(SurveyDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $data = $service->addSurvey($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action update survey
     *
     * @param UpdateSurveyDTO $request UpdateSurveyDTO data
     * @return Response
     *
     * @throws StorageException
     */
    #[Route('/survey/update/{uuid}', name: 'ctb-survey-admin-update', methods: 'PUT')]
    public function updateSurvey(UpdateSurveyDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $service->setUserId($this->getUser()->getId());
        $data = $service->updateSurvey($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action delete survey
     *
     * @param DeleteUuidDTO $request DeleteUuidDTO data
     * @return Response
     *
     * @throws StorageException
     */
    #[Route('/survey/delete/{uuid}', name: 'ctb-survey-admin-delete', methods: 'DELETE')]
    public function deleteSurvey(DeleteUuidDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $service->setUserId($this->getUser()->getId());
        $data = $service->deleteSurvey($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action add question
     *
     * @param QuestionAddDTO $request DTO
     * @return Response
     *
     * @throws StorageException
     */
    #[Route(path: '/survey/question/add', name: 'ctb-survey-admin-q-add')]
    public function addQuestion(QuestionAddDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $data = $service->addQuestion($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action update question
     *
     * @param QuestionUpdateDTO $request QuestionUpdateDTO data
     * @return Response
     *
     * @throws StorageException
     */
    #[Route('/survey/question/update/{id<\d+>}', name: 'ctb-survey-admin-q-update', methods: 'PUT')]
    public function updateQuestion(QuestionUpdateDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $data = $service->updateQuestion($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action delete question
     *
     * @param DeleteIdDTO $request DeleteUuidDTO data
     * @return Response
     *
     * @throws StorageException
     */
    #[Route('/survey/question/delete/{id<\d+>}', name: 'ctb-survey-admin-q-delete', methods: 'DELETE')]
    public function deleteQuestion(DeleteIdDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $service->setUserId($this->getUser()->getId());
        $data = $service->deleteQuestion($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action add answer
     *
     * @param AnswerAddDTO $request DTO
     * @return Response
     *
     * @throws StorageException
     */
    #[Route(path: '/survey/answer/add', name: 'ctb-survey-admin-a-add')]
    public function addAnswer(AnswerAddDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $data = $service->addAnswer($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action update answer
     *
     * @param AnswerUpdateDTO $request QuestionUpdateDTO data
     * @return Response
     *
     * @throws StorageException
     */
    #[Route('/survey/answer/update/{id<\d+>}', name: 'ctb-survey-admin-a-update', methods: 'PUT')]
    public function updateAnswer(AnswerUpdateDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $data = $service->updateAnswer($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action delete answer
     *
     * @param DeleteIdDTO $request DeleteUuidDTO data
     * @return Response
     *
     * @throws StorageException
     */
    #[Route('/survey/answer/delete/{id<\d+>}', name: 'ctb-survey-admin-a-delete', methods: 'DELETE')]
    public function deleteAnswer(DeleteIdDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $service->setUserId($this->getUser()->getId());
        $data = $service->deleteAnswer($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action add assignment
     *
     * @param AssignmentDTO $request DTO
     * @return Response
     *
     * @throws StorageException
     */
    #[Route(path: '/survey/assignment/add', name: 'ctb-survey-admin-am-add')]
    public function addAssignment(AssignmentDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $data = $service->addAssignment($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }
    /**
     * Action update assignment
     *
     * @param AssignmentUpdateDTO $request AssignmentDTO data
     * @return Response
     *
     * @throws StorageException
     */
    #[Route('/survey/assignment/update/{uuid}', name: 'ctb-survey-admin-am-update', methods: 'PUT')]
    public function updateAssigment(AssignmentUpdateDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $data = $service->updateAssignment($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action delete answer
     *
     * @param DeleteAssigmentDTO $request DeleteUuidDTO data
     * @return Response
     *
     * @throws StorageException
     */
    #[Route('/survey/assignment/delete/{id<\d+>}/{uuid}', name: 'ctb-survey-admin-am-delete', methods: 'DELETE')]
    public function deleteAssignment(DeleteAssigmentDTO $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $service = $this->surveyService;
        $service->setUserId($this->getUser()->getId());
        $data = $service->deleteAssigment($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }
}
