<?php

namespace cronv\Task\Management\Entity\Survey;

use cronv\Task\Management\Repository\Survey\QuestionRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "question")]
#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Question
{
    /** @var int ID */
    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    /** @var Survey Survey */
    #[ORM\ManyToOne(targetEntity: Survey::class, inversedBy: "survey")]
    #[ORM\JoinColumn(name: "survey_uuid", referencedColumnName: "uuid")]
    private Survey $survey;

    /** @var QuestionType Question type */
    #[ORM\ManyToOne(targetEntity: QuestionType::class, inversedBy: "question_type")]
    #[ORM\JoinColumn(name: "type_id", referencedColumnName: "id")]
    private QuestionType $questionType;

    /** @var string Name */
    #[ORM\Column(name: "name", type: Types::STRING, length: 255)]
    private string $name;

    /** @var ?DateTimeInterface Created */
    #[ORM\Column(name: "created_at", type: Types::DATETIME_MUTABLE, insertable: false, updatable: false)]
    private ?DateTimeInterface $createdAt;

    /** @var ?DateTimeInterface Updated */
    #[ORM\Column(name: "updated_at", type: Types::DATETIME_MUTABLE, insertable: false)]
    private ?DateTimeInterface $updatedAt;

    /**
     * Get the ID.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get text.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name Name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get question type.
     *
     * @return QuestionType
     */
    public function getQuestionType(): QuestionType
    {
        return $this->questionType;
    }

    /**
     * Set question type.
     *
     * @param QuestionType $questionType Question type
     * @return self
     */
    public function setQuestionType(QuestionType $questionType): self
    {
        $this->questionType = $questionType;
        return $this;
    }

    /**
     * Get survey.
     *
     * @return Survey
     */
    public function getSurvey(): Survey
    {
        return $this->survey;
    }

    /**
     * Set survey.
     *
     * @param Survey $survey Survey
     * @return self
     */
    public function setSurvey(Survey $survey): self
    {
        $this->survey = $survey;
        return $this;
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

    /**
     * Get the last update date and time of the task
     *
     * @return ?DateTimeInterface
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set update at
     *
     * @return void
     */
    #[ORM\PreUpdate]
    public function setUpdateAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
