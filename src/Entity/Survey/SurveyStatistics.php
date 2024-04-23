<?php

namespace cronv\Task\Management\Entity\Survey;

use cronv\Task\Management\Entity\User;
use cronv\Task\Management\Repository\TaskRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "survey_statistics")]
#[ORM\Entity(repositoryClass: TaskRepository::class)]
class SurveyStatistics
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

    /** @var int Number of questions */
    #[ORM\Column(name: "number_questions", type: Types::INTEGER)]
    private int $numberQuestions;

    /** @var int Number of correct */
    #[ORM\Column(name: "number_correct", type: Types::INTEGER)]
    private int $numberCorrect;

    /** @var DateTimeInterface Created */
    #[ORM\Column(name: "created_at", type: Types::DATETIME_MUTABLE, insertable: false, updatable: false)]
    private DateTimeInterface $createdAt;

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
     * Get the number of questions.
     *
     * @return int
     */
    public function getNumberQuestions(): int
    {
        return $this->numberQuestions;
    }

    /**
     * Get the number of correct.
     *
     * @return int
     */
    public function getNumberCorrect(): int
    {
        return $this->numberCorrect;
    }

    /**
     * Get the creation date and time of the task
     *
     * @return ?DateTimeInterface
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }
}
