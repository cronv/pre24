<?php

namespace cronv\Task\Management\DTO\Transaction;

use cronv\Task\Management\Component\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Transaction DTO
 */
class TransactionDTO extends AbstractJsonRequest
{
    /** @var int User ID */
    #[Assert\NotBlank]
    #[Assert\Type('int')]
    public int $userId;

    /** @var int User ID */
    #[Assert\NotBlank]
    #[Assert\Type('int')]
    public int $typeId;

    /** @var float Name */
    #[Assert\NotBlank]
    #[Assert\Type('float')]
    public float $amount;

    /** @var ?string Description */
    #[Assert\Type('string')]
    public ?string $description;

    /** @var string Date at */
    #[Assert\NotBlank]
    #[Assert\Date]
    public string $datedAt;
}
