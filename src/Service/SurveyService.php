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
use cronv\Task\Management\DTO\Survey\ProcessedDTO;
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
use cronv\Task\Management\Entity\Survey\SurveyResults;
use cronv\Task\Management\Entity\Survey\SurveyStatistics;
use cronv\Task\Management\Entity\Survey\SurveyMapping;
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
    /** @var string UUID new */
    protected string $newUuid;

    /** @var User User */
    protected User $user;

    /** @var Survey Survey */
    protected Survey $survey;

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
     * Get count statistics.
     *
     * @param string $uuid
     * @return int
     */
    public function statisticsCount(string $uuid): int
    {
        return $this->em->getRepository(SurveyStatistics::class)->count(['survey' => $uuid]);
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
        $std->final = false;
        $std->assignment = $this->findOneBy(SurveyAssignment::class, ['survey' => $params->uuid]);
        $std->statistics = $this->findBy(SurveyStatistics::class, ['survey' => $params->uuid]);

        if ($this->statisticsCount($params->uuid) < $std->assignment->getAttempts()) {
            $std->final = true;
        }

        return $std;
    }

    /**
     * List answer by question or answer ids.
     *
     * @param array $params Params
     * @return ?array
     */
    public function getSurveyAnswers(array $params): ?array
    {
        return $this->em->getRepository(Question::class)->getAnswers($params);
    }

    /**
     * Processed survey.
     *
     * @param ProcessedDTO $params DTO
     * @return object
     */
    public function processed(ProcessedDTO $params): object
    {
        $std = new \stdClass();
        $std->final = false;
        $std->page = $params->page;
        $std->uuid = $params->uuid;
        $next = $this->next(Survey::class, [
            'uuid' => $params->uuid,
            'page' => $params->page
        ]);
        $this->newUuid = $this->getLastUuid($params->uuid);
        $std->question = $next->pagination->getIterator()->current();
        $std->qCount = $this->questionCount(Survey::class, $params->uuid);
        $std->answers = $this->findBy(Answer::class, ['question' => $std->question['q_id']]);

        // TODO: correct line (step)
        $questionId = $std->question['q_id'];

        if ($params->page > 2 && $params->page < $std->qCount) {
            $next = $this->next(Survey::class, [
                'uuid' => $params->uuid,
                'page' => $params->page === 1 ? $params->page : $params->page - 1
            ]);
            $questionId = $next->pagination->getIterator()->current()['q_id'];
        }

        $listResults = $this->findBy(SurveyResults::class, [
            'uuid' => $this->newUuid,
            'user' => $params->userId,
            'survey' => $params->uuid,
            'question' => $questionId,
        ]);

        // insert/update result
        if ($params->radio || $params->textarea || $params->checkbox) {
            $key = $answerId = null;

            if ($params->radio) {
                $answerId = $key = $params->radio;
            } elseif ($params->textarea) {
                $answerFirst = $this->findOneBy(Answer::class, ['question' => $std->question['q_id']],
                    ['question' => 'ASC']);
                $answerId = $answerFirst->getId();
                $key = $params->textarea;
            }

            $this->user = $this->find($params->userId, User::class);
            $this->survey = $this->find($params->uuid, Survey::class);
            $question = $this->find($questionId, Question::class);

            if ($params->checkbox) {
                // clear pre-results [checkbox]
                $this->deleteMultipleRecords($listResults);

                $listAnswer = $this->getSurveyAnswers([
                    'uuid' => $params->uuid,
                    'id' => $questionId,
                ]);

                $results = [];
                foreach ($listAnswer as $answer) {
                    if (in_array($answer->getId(), array_keys($params->checkbox))
                        && ($validateAnswer = $this->getSurveyAnswers([
                        'uuid' => $params->uuid,
                        'id' => $questionId,
                        'ids' => [$answer->getId()]
                    ]))) {
                        $surveyResults = new SurveyResults(
                            uuid: $this->newUuid,
                            user: $this->user,
                            survey: $this->survey,
                            question: $question
                        );
                        $results[] = $surveyResults
                            ->setAnswer(array_shift($validateAnswer))
                            ->setText(null);
                    }
                }
            } else {
                if (!($results = $this->findOneBy(SurveyResults::class, [
                    'uuid' => $this->newUuid,
                    'user' => $params->userId,
                    'survey' => $params->uuid,
                    'question' => $std->question['q_id'],
                ]))) {
                    $results = new SurveyResults(
                        uuid: $this->newUuid,
                        user: $this->user,
                        survey: $this->survey,
                        question: $question
                    );
                }

                $answer = $this->find($answerId, Answer::class);

                $results->setAnswer($answer)->setText(null);
                if ($results->getQuestion()->getQuestionType()->getName() == 'textarea') {
                    $results->setText($key);
                }
            }

            // saved
            $this->store($results);
        }

        // update results
        $listResults = $this->findBy(SurveyResults::class, [
            'uuid' => $this->newUuid,
            'user' => $params->userId,
            'survey' => $params->uuid,
            'question' => $std->question['q_id'],
        ]);
        $std->results = array_map(fn($v) => $v->getAnswer()->getId(), $listResults) ?: [];

        // step
        $std->incPage = $params->page;
        if ($std->page < $std->qCount) {
            $std->incPage = $params->page + 1;
        } elseif ($std->page === $std->qCount && isset($params->send)) {
            $this->pushStatistics($params);
            $std->final = true;
        }

        return $std;
    }

    /**
     * Pushing statistics (final).
     *
     * @param ProcessedDTO $params Params
     * @return void
     */
    protected function pushStatistics(ProcessedDTO $params): void
    {
        $question = $this->question(Survey::class, $params->uuid);
        $std = new \stdClass();
        $std->numberQuest = sizeof($question);
        $std->correctAnswer = $this->correctCount(SurveyResults::class, $this->newUuid);
        $this->store(new SurveyStatistics(
            uuid: $this->newUuid,
            user: $this->user,
            survey: $this->survey,
            numberQuestions: $std->numberQuest,
            numberCorrect: $std->correctAnswer,
        ));
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
     * Get statistics (all).
     *
     * @return ?array
     */
    public function statistics(): ?array
    {
        return $this->em->getRepository(SurveyStatistics::class)->findAll();
    }

    /**
     * Get last UUID new.
     *
     * @param string $uuid UUID
     * @return string
     */
    public function getLastUuid(string $uuid): string
    {
        $statistics = $this->findOneBy(SurveyResults::class, ['uuid' => $uuid]);
        $uuidResults = $this->findOneBy(SurveyMapping::class, [
            'userId' => $this->getUserId(),
            'uuid' => $uuid
        ]);

        if (!$statistics && !$uuidResults) {
            $urEntity = new SurveyMapping(
                uuid: $uuid,
                userId: $this->getUserId()
            );
            try {
                $this->store($urEntity);
            } catch (StorageException $e) {}
            return $urEntity->getUuidNew();
        }

        return $uuidResults->getUuidNew();
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
