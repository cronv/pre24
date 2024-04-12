<?php

namespace cronv\Task\Management\Entity;

use cronv\Task\Management\Repository\TaskRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\UuidV4;

#[ORM\Table(name: "tasks")]
#[ORM\Index(columns: ["user_id"], name: "idx_tasks__user_id")]
#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    /** @var string Task ID
     * @since https://github.com/doctrine/orm/issues/7312
     */
    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::GUID)]
    private string $id;

    /** @var ?User ID of the user to whom the task is linked */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "users")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private ?User $user;

    /** @var string Name */
    #[ORM\Column(name: "name", type: Types::STRING, length: 255)]
    public string $name;

    /** @var ?string Description */
    #[ORM\Column(name: "description", type: Types::TEXT)]
    public ?string $description = null;

    /** @var ?DateTimeInterface Deadline */
    #[ORM\Column(name: "deadline", type: Types::DATE_MUTABLE, nullable: true)]
    public ?DateTimeInterface $deadline = null;

    /** @var ?DateTimeInterface Created */
    #[ORM\Column(name: "created_at", type: Types::DATETIME_MUTABLE, insertable: false)]
    public ?DateTimeInterface $createdAt;

    /** @var ?DateTimeInterface Updated */
    #[ORM\Column(name: "updated_at", type: Types::DATETIME_MUTABLE, insertable: false)]
    public ?DateTimeInterface $updatedAt;

    /**
     * Task constructor.
     */
    public function __construct()
    {
        $this->id = UuidV4::v4();
    }

    /**
     * Get the ID of the task.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the ID of the user associated with the task.
     *
     * @return ?User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set the ID of the user associated with the task.
     *
     * @param ?User $user
     * @return Task
     */
    public function setUser(?User $user): Task
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get the name of the task.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name of the task.
     *
     * @param string $name
     * @return Task
     */
    public function setName(string $name): Task
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the description of the task.
     *
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description of the task.
     *
     * @param ?string $description
     * @return Task
     */
    public function setDescription(?string $description): Task
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the deadline of the task.
     *
     * @return ?DateTimeInterface
     */
    public function getDeadline(): ?DateTimeInterface
    {
        return $this->deadline;
    }

    /**
     * Set the deadline of the task.
     *
     * @param ?DateTimeInterface $deadline
     * @return Task
     */
    public function setDeadline(?DateTimeInterface $deadline): Task
    {
        $this->deadline = $deadline;
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
}
