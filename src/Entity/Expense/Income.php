<?php

namespace cronv\Task\Management\Entity\Expense;

use cronv\Task\Management\Entity\User;
use cronv\Task\Management\Repository\Expense\IncomeRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "income")]
#[ORM\Entity(repositoryClass: IncomeRepository::class)]
class Income
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
     * @param string $source Source
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

        #[ORM\Column(name: "source", type: Types::STRING, length: 255)]
        private readonly string $source,

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
     * Get source.
     *
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
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
