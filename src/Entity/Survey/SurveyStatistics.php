<?php

namespace cronv\Task\Management\Entity\Survey;

use cronv\Task\Management\Entity\User;
use cronv\Task\Management\Repository\Survey\SurveyStatisticsRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

#[ORM\Table(name: "survey_statistics")]
#[ORM\Entity(repositoryClass: SurveyStatisticsRepository::class)]
class SurveyStatistics
{
    /** @var DateTimeInterface Created */
    #[ORM\Column(name: "created_at", type: Types::DATETIME_MUTABLE, insertable: false, updatable: false)]
    private DateTimeInterface $createdAt;

    /**
     * SurveyResults constructor.
     */
    /**
     * @param string $uuid UUID
     * @param User $user User
     * @param Survey $survey Survey
     * @param int $numberQuestions Number of questions
     * @param int $numberCorrect Number of correct
     */
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(name: "uuid", type: Types::GUID)]
        private readonly string $uuid,

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "users")]
        #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
        private readonly User $user,

        #[ORM\ManyToOne(targetEntity: Survey::class, inversedBy: "survey")]
        #[ORM\JoinColumn(name: "survey_uuid", referencedColumnName: "uuid")]
        private readonly Survey $survey,

        #[ORM\Column(name: "number_questions", type: Types::INTEGER)]
        private readonly int $numberQuestions,

        #[ORM\Column(name: "number_correct", type: Types::INTEGER)]
        private readonly int $numberCorrect,
    )
    {
    }

    /**
     * Get the UUID.
     *
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Get the User.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get the Survey.
     *
     * @return Survey
     */
    public function getSurvey(): Survey
    {
        return $this->survey;
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
