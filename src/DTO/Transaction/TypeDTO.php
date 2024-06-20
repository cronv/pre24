<?php

namespace cronv\Task\Management\DTO\Transaction;

use cronv\Task\Management\Component\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Type (transaction) DTO
 */
class TypeDTO extends AbstractJsonRequest
{
    /** @var string Name */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 64)]
    public string $name;
}
