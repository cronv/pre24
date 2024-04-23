<?php

namespace cronv\Task\Management\Entity\Survey;

use cronv\Task\Management\Entity\User;
use cronv\Task\Management\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "survey_results")]
#[ORM\Entity(repositoryClass: TaskRepository::class)]
class SurveyResults
{
    /** @var User User */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "users")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private User $userId;

    /** @var Survey Survey */
    #[ORM\ManyToOne(targetEntity: Survey::class, inversedBy: "survey")]
    #[ORM\JoinColumn(name: "survey_uuid", referencedColumnName: "uuid")]
    private Survey $surveyUuid;

    /** @var Question Question */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "question")]
    #[ORM\JoinColumn(name: "question_id", referencedColumnName: "id")]
    private Question $questionId;

    /** @var Answer Answer */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "answer")]
    #[ORM\JoinColumn(name: "answer_id", referencedColumnName: "id")]
    private Answer $answerId;

    /**
     * Get the User.
     *
     * @return User
     */
    public function getUserId(): User
    {
        return $this->userId;
    }

    /**
     * Get the Survey.
     *
     * @return Survey
     */
    public function getSurveyUuid(): Survey
    {
        return $this->surveyUuid;
    }

    /**
     * Get the Question.
     *
     * @return Question
     */
    public function getQuestionId(): Question
    {
        return $this->questionId;
    }

    /**
     * Get the Answer.
     *
     * @return Answer
     */
    public function getAnswerId(): Answer
    {
        return $this->answerId;
    }
}
