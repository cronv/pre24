<?php

namespace cronv\Task\Management\Entity\Transaction;

use cronv\Task\Management\Repository\Transaction\TransactionTypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "transaction_type")]
#[ORM\Entity(repositoryClass: TransactionTypeRepository::class)]
class TransactionType
{
    /** @var int ID */
    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    /** @var string Name */
    #[ORM\Column(name: "name", type: Types::STRING, length: 64)]
    private string $name;

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
     * Get name.
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
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
