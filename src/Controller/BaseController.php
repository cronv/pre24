<?php

namespace cronv\Task\Management\Controller;

use cronv\Task\Management\DTO\DeleteUuidDTO;
use cronv\Task\Management\DTO\Survey\ParamsDTO;
use cronv\Task\Management\DTO\Survey\QuestionAddDTO;
use cronv\Task\Management\DTO\Survey\QuestionUpdateDTO;
use cronv\Task\Management\DTO\Survey\SurveyDTO;
use cronv\Task\Management\DTO\Survey\UpdateSurveyDTO;
use cronv\Task\Management\Exception\StorageException;
use cronv\Task\Management\Service\SurveyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller base
 */
class BaseController extends AbstractController
{
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
