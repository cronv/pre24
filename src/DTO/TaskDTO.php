<?php

namespace cronv\Task\Management\DTO;

use cronv\Task\Management\Component\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Task DTO
 */
class TaskDTO extends AbstractJsonRequest
{
    /** @var string $name Name */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 255)]
    public string $name;

    /** @var ?string $description Description */
    #[Assert\Type('string')]
    public ?string $description;

    /** @var ?string $deadline Deadline */
    #[Assert\Date]
    public ?string $deadline;
}
