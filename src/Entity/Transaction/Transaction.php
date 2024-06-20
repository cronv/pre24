<?php

namespace cronv\Task\Management\Entity\Transaction;

use cronv\Task\Management\Entity\User;
use cronv\Task\Management\Repository\Transaction\TransactionRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "transaction")]
#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    /** @var int ID */
    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    /** @var TransactionType Transaction type */
    #[ORM\ManyToOne(targetEntity: TransactionType::class, inversedBy: "transaction_type")]
    #[ORM\JoinColumn(name: "type_id", referencedColumnName: "id")]
    private TransactionType $type;

    /** @var float Amount */
    #[ORM\Column(name: "amount", type: Types::DECIMAL, precision: 10, scale: 2)]
    private float $amount;

    /** @var ?string Description */
    #[ORM\Column(name: "description", type: Types::TEXT, nullable: true)]
    private ?string $description;

    /** @var DateTimeInterface Dated at */
    #[ORM\Column(name: "dated_at", type: Types::DATE_MUTABLE)]
    public DateTimeInterface $datedAt;

    /** @var DateTimeInterface Created */
    #[ORM\Column(name: "created_at", type: Types::DATETIME_MUTABLE, insertable: false, updatable: false)]
    public DateTimeInterface $createdAt;

    /**
     * Expense constructor.
     *
     * @param User $user User
     * @return void
     */
    public function __construct(
        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "users")]
        #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
        private readonly User $user,
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
     * Get transaction type.
     *
     * @return TransactionType
     */
    public function getType(): TransactionType
    {
        return $this->type;
    }

    /**
     * Set transaction type.
     *
     * @param TransactionType $type Transaction type
     * @return self
     */
    public function setType(TransactionType $type): self
    {
        $this->type = $type;
        return $this;
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
     * Set amount.
     *
     * @param float $amount Amount
     * @return self
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get description.
     *
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param ?string $description Description
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get date at.
     *
     * @return DateTimeInterface
     */
    public function getDateAt(): DateTimeInterface
    {
        return $this->datedAt;
    }

    /**
     * Set date.
     *
     * @param DateTimeInterface $datedAt Date at
     * @return self
     */
    public function setDatedAt(DateTimeInterface $datedAt): self
    {
        $this->datedAt = $datedAt;
        return $this;
    }

    /**
     * Get created at.
     *
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
