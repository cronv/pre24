<?php

namespace cronv\Task\Management\Controller;

use cronv\Task\Management\DTO\DeleteTaskDTO;
use cronv\Task\Management\DTO\TaskDTO;
use cronv\Task\Management\DTO\UpdateTaskDTO;
use cronv\Task\Management\Exception\StorageException;
use cronv\Task\Management\Service\TaskService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validation;

/**
 * Controller tasks
 */
class TaskController extends AbstractController
{
    /**
     * TaskController constructor
     *
     * @param TaskService $serviceTask Service Task
     */
    public function __construct(
        protected readonly TaskService $serviceTask,
    )
    {
    }

    /**
     * Action tasks management
     *
     * @param int $page Number page
     * @return Response
     */
    #[Route('/task/tasks/{page<\d+>}', name: 'cronv-tm-bundle', defaults: ['page' => 1], methods: ['GET', 'POST'])]
    public function tasks(int $page): Response
    {
        $service = $this->serviceTask;
        $service->setUserId($this->getUser()->getId());
        $object = $this->serviceTask->list($page);
        return $this->render('index.html.twig', [
            'pagination' => $object->pagination,
            'paginator_render' => $this->renderView('@cronvTaskManagement/paginator/paginator.html.twig', [
                'total' => $object->total,
                'lastPage' => $object->lastPage,
                'page' => $object->page,
            ]),
        ]);
    }

    /**
     * Action add task
     *
     * @param TaskDTO $request TaskDTO data
     * @return JsonResponse
     * @throws StorageException
     */
    #[Route('/task/add', name: 'cronv-tm-bundle-add', methods: 'POST')]
    public function add(TaskDTO $request): JsonResponse
    {
        $service = $this->serviceTask;
        $service->setUserId($this->getUser()->getId());
        $data = $service->add($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action update task
     *
     * @param UpdateTaskDTO $request UpdateTaskDTO data
     * @return JsonResponse
     *
     * @throws StorageException
     */
    #[Route('/task/update/{uuid}', name: 'cronv-tm-bundle-update', methods: 'PUT')]
    public function update(UpdateTaskDTO $request): JsonResponse
    {
        $service = $this->serviceTask;
        $service->setUserId($this->getUser()->getId());
        $data = $service->update($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Action delete task
     *
     * @param DeleteTaskDTO $request DeleteTaskDTO data
     * @return Response
     *
     * @throws StorageException
     */
    #[Route('/task/delete/{uuid}', name: 'cronv-tm-bundle-delete', methods: 'DELETE')]
    public function delete(DeleteTaskDTO $request): Response
    {
        $service = $this->serviceTask;
        $service->setUserId($this->getUser()->getId());
        $data = $service->delete($request);
        $status = $service->getHttpCode();

        return $this->json($data, $status);
    }

    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode
     *
     * @param int $status The HTTP status code (200 "OK" by default)
     */
    protected function json(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        $context = array_merge($context, [
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS | JSON_UNESCAPED_UNICODE
        ]);
        return parent::json($data, $status, $headers, $context);
    }
}
