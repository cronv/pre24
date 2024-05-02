<?php

namespace cronv\Task\Management\Entity\Expense;

use cronv\Task\Management\Entity\User;
use cronv\Task\Management\Repository\Expense\ExpenseRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "expense")]
#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
class Expense
{
    /** @var int ID */
    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    /** @var DateTimeInterface Created */
    #[ORM\Column(name: "created_at", type: Types::DATETIME_MUTABLE, insertable: false, updatable: false)]
    public DateTimeInterface $createdAt;

    /**
     * Expense constructor.
     *
     * @param User $user User
     * @param float $amount Amount
     * @param string $category Category
     * @param string $description Description
     * @param DateTimeInterface $date Date
     * @return void
     */
    public function __construct(
        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "users")]
        #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
        private readonly User $user,

        #[ORM\Column(name: "amount", type: Types::DECIMAL, precision: 10, scale: 2)]
        private readonly float $amount,

        #[ORM\Column(name: "category", type: Types::STRING, length: 255)]
        private readonly string $category,

        #[ORM\Column(name: "description", type: Types::TEXT)]
        private readonly string $description,

        #[ORM\Column(name: "date", type: Types::DATE_MUTABLE)]
        public readonly DateTimeInterface $date
    )
    {
    }

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
     * Get user.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get amount.
     *
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Get category.
     *
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get date.
     *
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
