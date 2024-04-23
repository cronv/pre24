<?php

namespace cronv\Task\Management\Entity\Survey;

use cronv\Task\Management\Repository\Survey\AnswerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "answer")]
#[ORM\Entity(repositoryClass: AnswerRepository::class)]
class Answer
{
    /** @var int ID */
    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    /** @var string Value */
    #[ORM\Column(name: "value", type: Types::TEXT)]
    private string $value;

    /** @var bool Is correct */
    #[ORM\Column(name: "is_correct", type: Types::BOOLEAN)]
    private bool $isCorrect;

    /** @var Question Question */
    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: "question")]
    #[ORM\JoinColumn(name: "question_id", referencedColumnName: "id")]
    private Question $question;

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
     * Get value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set value.
     *
     * @param string $value Value
     * @return self
     */
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get is correct.
     *
     * @return bool
     */
    public function getIsCorrect(): bool
    {
        return $this->isCorrect;
    }

    /**
     * Set is correct.
     *
     * @param string $isCorrect Is correct string
     * @return self
     */
    public function setIsCorrect(string $isCorrect): self
    {
        $this->isCorrect = $isCorrect == 'true';
        return $this;
    }

    /**
     * Set the ID of the user associated with the task.
     *
     * @param Question $question Entity Question
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * Set question.
     *
     * @param Question $question Question
     * @return self
     */
    public function setQuestion(Question $question): self
    {
        $this->question = $question;
        return $this;
    }
}
