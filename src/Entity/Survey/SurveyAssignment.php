<?php

namespace cronv\Task\Management\Entity\Survey;

use cronv\Task\Management\Entity\User;
use cronv\Task\Management\Repository\Survey\SurveyAssignmentRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "survey_assignment")]
#[ORM\Entity(repositoryClass: SurveyAssignmentRepository::class)]
class SurveyAssignment
{
    /** @var User User */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "users")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private User $user;

    /** @var Survey Survey UUID */
    #[ORM\ManyToOne(targetEntity: Survey::class, inversedBy: "survey")]
    #[ORM\JoinColumn(name: "survey_uuid", referencedColumnName: "uuid")]
    private Survey $survey;

    /** @var ?int Number of attempts */
    #[ORM\Column(name: "attempts", type: Types::INTEGER, nullable: true)]
    private ?int $attempts;

    /** @var ?bool Access */
    #[ORM\Column(name: "access", type: Types::BOOLEAN, nullable: true)]
    private ?bool $access;

    /** @var ?DateTimeInterface Started */
    #[ORM\Column(name: "started_at", type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $startedAt;

    /** @var ?DateTimeInterface Ended */
    #[ORM\Column(name: "ended_at", type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $endedAt;

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
     * Set user.
     *
     * @param User $user User
     * @return self
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
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
     * Set survey.
     *
     * @param Survey $survey Survey
     * @return $this
     */
    public function setSurvey(Survey $survey): self
    {
        $this->survey = $survey;
        return $this;
    }

    /**
     * Get number of attempts.
     *
     * @return ?int
     */
    public function getAttempts(): ?int
    {
        return $this->attempts;
    }

    /**
     * Set attempts.
     *
     * @param ?int $attempts Attempts
     * @return $this
     */
    public function setAttempts(?int $attempts): self
    {
        $this->attempts = $attempts;
        return $this;
    }

    /**
     * Get access.
     *
     * @return ?bool
     */
    public function getAccess(): ?bool
    {
        return $this->access;
    }

    /**
     * Set access.
     *
     * @param ?int $access Access
     * @return $this
     */
    public function setAccess(?int $access): self
    {
        $this->attempts = $access;
        return $this;
    }

    /**
     * Get the started date
     *
     * @return ?DateTimeInterface
     */
    public function getStartedAt(): ?DateTimeInterface
    {
        return $this->startedAt;
    }

    /**
     * Set the started at.
     *
     * @param ?DateTimeInterface $startedAt Started at
     * @return self
     */
    public function setStartedAt(?DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    /**
     * Get the last ended date
     *
     * @return ?DateTimeInterface
     */
    public function getEndedAt(): ?DateTimeInterface
    {
        return $this->endedAt;
    }

    /**
     * Set the ended at.
     *
     * @param ?DateTimeInterface $endedAt Ended at
     * @return self
     */
    public function setEndedAt(?DateTimeInterface $endedAt): self
    {
        $this->startedAt = $endedAt;
        return $this;
    }

    /**
     * Get color status date.
     *
     * @return string
     */
    public function getColor(): string
    {
        $color = 'table-grey';
        $dateNow = new \DateTime;

        if ($this->getEndedAt() && $dateNow < $this->getEndedAt()) {
            $color = 'table-danger';
        } elseif ($this->getEndedAt() && $dateNow > $this->getEndedAt()) {
            $color = 'table-success';
        }

        return $color;
    }
}
