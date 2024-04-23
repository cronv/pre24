<?php

namespace cronv\Task\Management\Service;


use cronv\Task\Management\DTO\DeleteIdDTO;
use cronv\Task\Management\DTO\DeleteUuidDTO;
use cronv\Task\Management\DTO\PaginatorDTO;
use cronv\Task\Management\DTO\Survey\AnswerAddDTO;
use cronv\Task\Management\DTO\Survey\AnswerDTO;
use cronv\Task\Management\DTO\Survey\AnswerUpdateDTO;
use cronv\Task\Management\DTO\Survey\AssignmentDTO;
use cronv\Task\Management\DTO\Survey\AssignmentUpdateDTO;
use cronv\Task\Management\DTO\Survey\DeleteAssigmentDTO;
use cronv\Task\Management\DTO\Survey\ParamsDTO;
use cronv\Task\Management\DTO\Survey\QuestionAddDTO;
use cronv\Task\Management\DTO\Survey\QuestionUpdateDTO;
use cronv\Task\Management\DTO\Survey\SurveyDTO;
use cronv\Task\Management\DTO\ResponseDTO;
use cronv\Task\Management\DTO\Survey\UpdateSurveyDTO;
use cronv\Task\Management\Entity\Survey\Answer;
use cronv\Task\Management\Entity\Survey\Question;
use cronv\Task\Management\Entity\Survey\QuestionType;
use cronv\Task\Management\Entity\Survey\Survey;
use cronv\Task\Management\Entity\Survey\SurveyAssignment;
use cronv\Task\Management\Entity\Task;
use cronv\Task\Management\Entity\User;
use cronv\Task\Management\Exception\StorageException;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validation;

/**
 * Service Survey
 */
class SurveyService extends BaseService
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
     * List task
     *
     * @param int $page Number page
     * @return PaginatorDTO
     */
    public function listSurvey(int $page): PaginatorDTO
    {
        return $this->em->getRepository(Survey::class)->findPaginatedResults(['page' => $page]);
    }

    /**
     * List question
     *
     * @param ParamsDTO $params List params DTO
     * @return PaginatorDTO
     */
    public function listQuestion(ParamsDTO $params): PaginatorDTO
    {
        return $this->em->getRepository(Question::class)->findPaginatedResults([
            'uuid' => $params->uuid,
            'page' => $params->page
        ]);
    }

    /**
     * List answer
     *
     * @param ParamsDTO $params List params DTO
     * @return PaginatorDTO
     */
    public function listAnswer(ParamsDTO $params): PaginatorDTO
    {
        return $this->em->getRepository(Answer::class)->findPaginatedResults([
            'question_id' => $params->id,
            'page' => $params->page
        ]);
    }

    /**
     * List assignment
     *
     * @param ParamsDTO $params List params DTO
     * @return PaginatorDTO
     */
    public function listAssignment(ParamsDTO $params): PaginatorDTO
    {
        return $this->em->getRepository(SurveyAssignment::class)->findPaginatedResults([
            'page' => $params->page,
            'userId' => $params->userId ?? null
        ]);
    }

    /**
     * Info attempts.
     *
     * @param ParamsDTO $params Params DTO
     * @return object
     */
    public function infoAttempts(ParamsDTO $params): object
    {
        $std = new \stdClass();
        $std->assignment = $this->findOneBy(SurveyAssignment::class, ['survey' => $params->uuid]);
        // TODO: results, statistics

        return $std;
    }

    /**
     * Processed survey.
     *
     * @param ParamsDTO $params Params DTO
     * @return object
     */
    public function processed(ParamsDTO $params): object
    {
        $std = new \stdClass();
        $next = $this->next(Survey::class, [
            'uuid' => $params->uuid,
            'page' => $params->page
        ]);
        $std->question = $next->pagination->getIterator()->current();
        $std->answers = $this->findBy(Answer::class, ['question' => $std->question['q_id']]);
        return $std;
    }

    /**
     * Add survey
     *
     * @param SurveyDTO $request SurveyDTO data
     * @return ResponseDTO
     *
     * @throws StorageException
     */
    public function addSurvey(SurveyDTO $request): ResponseDTO
    {
        if ($survey = $this->findName($request->name, Survey::class)) {
            $this->setHttpCode(Response::HTTP_CONFLICT);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такой ресурс уже существует!']
            );
        }

        $entitySurvey = new Survey();
        $entitySurvey->setName($request->name);

        $this->store($entitySurvey);
        $this->setHttpCode(Response::HTTP_CREATED);

        return new ResponseDTO(
            message: sprintf('Анкета `%s` успешно создана.', $entitySurvey->getUuid()),
            errors: []
        );
    }

    /**
     * Add question
     *
     * @param QuestionAddDTO $request QuestionAddDTO data
     * @return ResponseDTO
     *
     * @throws StorageException
     */
    public function addQuestion(QuestionAddDTO $request): ResponseDTO
    {
        if ($question = $this->findName([$request->uuid, $request->name], Question::class)) {
            $this->setHttpCode(Response::HTTP_CONFLICT);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такой ресурс уже существует!']
            );
        }

        if (!($type = $this->findName($request->type, QuestionType::class))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого типа не существует!']
            );
        }

        if (!($survey = $this->find($request->uuid, Survey::class))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такой анкеты не существует!']
            );
        }

        $entityQuestion = new Question();
        $entityQuestion->setName($request->name)
        ->setQuestionType($type)
        ->setSurvey($survey);

        $this->store($entityQuestion);
        $this->setHttpCode(Response::HTTP_CREATED);

        return new ResponseDTO(
            message: sprintf('Вопрос `%s` успешно создан.', $entityQuestion->getId()),
            errors: []
        );
    }

    /**
     * Add answer
     *
     * @param AnswerDTO $request DTO
     * @return ResponseDTO
     *
     * @throws StorageException
     */
    public function addAnswer(AnswerDTO $request): ResponseDTO
    {
        if ($answer = $this->findName([$request->id, $request->name], Answer::class)) {
            $this->setHttpCode(Response::HTTP_CONFLICT);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такой ресурс уже существует!']
            );
        }

        if (!($question = $this->find($request->id, Question::class))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого вопроса не существует!']
            );
        }

        $entityAnswer = new Answer();
        $entityAnswer->setValue($request->name)
            ->setIsCorrect($request->isCorrect)
            ->setQuestion($question);

        $this->store($entityAnswer);
        $this->setHttpCode(Response::HTTP_CREATED);

        return new ResponseDTO(
            message: sprintf('Ответ `%s` успешно создан.', $entityAnswer->getId()),
            errors: []
        );
    }

    /**
     * Add answer
     *
     * @param AssignmentDTO $request DTO
     * @return ResponseDTO
     *
     * @throws StorageException
     */
    public function addAssignment(AssignmentDTO $request): ResponseDTO
    {
        if ($assignment = $this->findName([$request->userId, $request->surveyId], SurveyAssignment::class)) {
            $this->setHttpCode(Response::HTTP_CONFLICT);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такой ресурс уже существует!']
            );
        }

        if (!($user = $this->find($request->userId, User::class))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого пользователя не существует!']
            );
        }

        if (!($survey = $this->find($request->surveyId, Survey::class))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такой анкеты не существует!']
            );
        }

        $entityAssignment = new SurveyAssignment();
        $entityAssignment->setUser($user)
            ->setSurvey($survey)
            ->setAttempts($request->attempts)
            ->setAccess($request->access);

        if ($request->startedAt) {
            $entityAssignment->setStartedAt(new DateTime($request->startedAt));
        }

        if ($request->endedAt) {
            $entityAssignment->setEndedAt(new DateTime($request->endedAt));
        }

        $this->store($entityAssignment);
        $this->setHttpCode(Response::HTTP_CREATED);

        return new ResponseDTO(
            message: sprintf('Назначение `%s` успешно создан.', $entityAssignment->getUser()->getUsername()),
            errors: []
        );
    }

    /**
     * Update survey
     *
     * @param UpdateSurveyDTO $request UpdateSurveyDTO data
     * @return object
     *
     * @throws StorageException
     */
    public function updateSurvey(UpdateSurveyDTO $request): object
    {
        $validator = Validation::createValidator();
        $validator->validate($request);

        if (!($survey = $this->find($request->uuid, Survey::class))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого ресурса не существует!']
            );
        }

        $surveyName = $this->findName($request->name, Survey::class);
        if ($surveyName && $surveyName->getUuid() !== $request->uuid) {
            $this->setHttpCode(Response::HTTP_CONFLICT);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такая анкета уже существует.']
            );
        }

        $entitySurvey = $survey;
        $entitySurvey->setName($request->name);

        $this->store($entitySurvey);
        $this->setHttpCode(Response::HTTP_CREATED);

        return new ResponseDTO(
            message: sprintf('Ресурс `%s` успешно обновлен.', $entitySurvey->getName()),
            errors: []
        );
    }

    /**
     * Update question
     *
     * @param QuestionUpdateDTO $request QuestionUpdateDTO data
     * @return object
     *
     * @throws StorageException
     */
    public function updateQuestion(QuestionUpdateDTO $request): object
    {
        $validator = Validation::createValidator();
        $validator->validate($request);

        if (!($type = $this->findName($request->type, QuestionType::class))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого типа не существует!']
            );
        }

        if (!($question = $this->findOneBy(Question::class, [
            'id' => $request->id,
            'survey' => $request->uuid,
        ]))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого ресурса не существует!']
            );
        }

        $questionName = $this->findName([$request->uuid, $request->name], Question::class);
        if ($questionName
            && $questionName->getQuestionType()->getName() === $request->type
        ) {
            $this->setHttpCode(Response::HTTP_CONFLICT);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такой вопрос уже существует.']
            );
        }

        $entityQuestion = $question;
        $entityQuestion->setName($request->name)
        ->setQuestionType($type);

        $this->store($entityQuestion);
        $this->setHttpCode(Response::HTTP_CREATED);

        return new ResponseDTO(
            message: sprintf('Ресурс `%s` успешно обновлен.', $entityQuestion->getName()),
            errors: []
        );
    }

    /**
     * Update answer
     *
     * @param AnswerUpdateDTO $request DTO
     * @return ResponseDTO
     *
     * @throws StorageException
     */
    public function updateAnswer(AnswerUpdateDTO $request): ResponseDTO
    {
        if (!($answer = $this->find($request->id, Answer::class))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого вопроса не существует!']
            );
        }

        $entityQuestion = $answer;
        $entityQuestion
            ->setValue($request->name)
            ->setIsCorrect($request->isCorrect);

        $this->store($entityQuestion);
        $this->setHttpCode(Response::HTTP_CREATED);

        return new ResponseDTO(
            message: sprintf('Вопрос `%s` успешно обновлен.', $entityQuestion->getId()),
            errors: []
        );
    }

    /**
     * Update assignment
     *
     * @param AssignmentUpdateDTO $request DTO
     * @return ResponseDTO
     *
     * @throws StorageException
     */
    public function updateAssignment(AssignmentUpdateDTO $request): ResponseDTO
    {
        if (!($assignment = $this->findOneBy(SurveyAssignment::class, [
            'user' => $request->userId,
            'survey' => $request->uuid
        ]))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: null,
                errors: ['name' => 'Такого назначения не существует!']
            );
        }

        $entityAssignment = $assignment
            ->setAttempts($request->attempts)
            ->setAccess($request->access);

        if ($request->startedAt) {
            $entityAssignment->setStartedAt(new DateTime($request->startedAt));
        }

        if ($request->endedAt) {
            $entityAssignment->setEndedAt(new DateTime($request->endedAt));
        }

        $this->store($entityAssignment);
        $this->setHttpCode(Response::HTTP_CREATED);

        return new ResponseDTO(
            message: sprintf('Назначение `%s` успешно обновлено.', $entityAssignment->getUser()->getUsername()),
            errors: []
        );
    }

    /**
     * Delete survey
     *
     * @param DeleteUuidDTO $request DeleteUuidDTO data
     * @return object
     *
     * @throws StorageException
     */
    public function deleteSurvey(DeleteUuidDTO $request): object
    {
        $validator = Validation::createValidator();
        $validator->validate($request);

        if (!($survey = $this->em->getRepository(Survey::class)->find($request->uuid))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: sprintf('Ресурса `%s` не существует.', $request->uuid),
                errors: []
            );
        }

        $this->deleteMultipleRecords($survey);

        $this->setHttpCode(Response::HTTP_NO_CONTENT);
        return new ResponseDTO(
            message: 'Ресурс успешно удален.',
            errors: []
        );
    }

    /**
     * Delete question
     *
     * @param DeleteIdDTO $request DeleteUuidDTO data
     * @return object
     *
     * @throws StorageException
     */
    public function deleteQuestion(DeleteIdDTO $request): object
    {
        $validator = Validation::createValidator();
        $validator->validate($request);

        if (!($question = $this->em->getRepository(Question::class)->find($request->id))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: sprintf('Ресурса `%s` не существует.', $request->id),
                errors: []
            );
        }

        $this->deleteMultipleRecords($question);

        $this->setHttpCode(Response::HTTP_NO_CONTENT);
        return new ResponseDTO(
            message: 'Ресурс успешно удален.',
            errors: []
        );
    }

    /**
     * Delete answer
     *
     * @param DeleteIdDTO $request DeleteUuidDTO data
     * @return object
     *
     * @throws StorageException
     */
    public function deleteAnswer(DeleteIdDTO $request): object
    {
        $validator = Validation::createValidator();
        $validator->validate($request);

        if (!($answer = $this->em->getRepository(Answer::class)->find($request->id))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: sprintf('Ресурса `%s` не существует.', $request->id),
                errors: []
            );
        }

        $this->deleteMultipleRecords($answer);

        $this->setHttpCode(Response::HTTP_NO_CONTENT);
        return new ResponseDTO(
            message: 'Ресурс успешно удален.',
            errors: []
        );
    }

    /**
     * Delete assigment
     *
     * @param DeleteAssigmentDTO $request DeleteAssigmentDTO data
     * @return object
     *
     * @throws StorageException
     */
    public function deleteAssigment(DeleteAssigmentDTO $request): object
    {
        $validator = Validation::createValidator();
        $validator->validate($request);

        if (!($assigment = $this->findOneBy(SurveyAssignment::class, [
            'user' => $request->id,
            'survey' => $request->uuid
        ]))) {
            $this->setHttpCode(Response::HTTP_NOT_FOUND);
            return new ResponseDTO(
                message: sprintf('Ресурса `%s` не существует.', $request->id),
                errors: []
            );
        }

        $this->deleteMultipleRecords($assigment);

        $this->setHttpCode(Response::HTTP_NO_CONTENT);
        return new ResponseDTO(
            message: 'Ресурс успешно удален.',
            errors: []
        );
    }

    /**
     * List question types.
     *
     * @return array<QuestionType>
     */
    public function getQuestionTypes(): array
    {
        return $this->em->getRepository(QuestionType::class)->findAll();
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

    /**
     * List survey.
     *
     * @return array<Survey>
     */
    public function getSurvey(): array
    {
        return $this->em->getRepository(Survey::class)->findAll();
    }

    /**
     * List corrects.
     *
     * @return string[]
     */
    public function getCorrects(): array
    {
        return [
            'false' => 'Нет',
            'true' => 'Да',
        ];
    }
}
