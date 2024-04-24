<?php

namespace cronv\Task\Management\Entity\Survey;

use cronv\Task\Management\Entity\User;
use cronv\Task\Management\Repository\Survey\SurveyResultsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "survey_results")]
#[ORM\Entity(repositoryClass: SurveyResultsRepository::class)]
class SurveyResults
{
    /** @var Answer Answer */
    #[ORM\ManyToOne(targetEntity: Answer::class, inversedBy: "answer")]
    #[ORM\JoinColumn(name: "answer_id", referencedColumnName: "id")]
    private Answer $answer;

    /** @var string Вводимый ответ */
    #[ORM\Column(name: "text", type: Types::STRING, nullable: true)]
    private string $text;

    /**
     * SurveyResults constructor.
     *
     * @param string $uuid UUID
     * @param User $user User
     * @param Survey $survey Survey
     * @param Question $question Question
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

        #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: "question")]
        #[ORM\JoinColumn(name: "question_id", referencedColumnName: "id")]
        private readonly Question $question
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
     * Get the Question.
     *
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * Get the Answer.
     *
     * @return Answer
     */
    public function getAnswer(): Answer
    {
        return $this->answer;
    }

    /**
     * Set answer.
     *
     * @param Answer $answer Answer
     * @return void
     */
    public function setAnswer(Answer $answer): void
    {
        $this->answer = $answer;
    }

    /**
     * Get text.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Set text.
     *
     * @param string $text Text (answer)
     * @return void
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }
}
