<?php

namespace cronv\Task\Management\DTO\Survey;

use cronv\Task\Management\Component\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Processed DTO
 */
class ProcessedDTO extends ParamsDTO
{
    /** @var ?array Checkbox */
    public ?array $checkbox = [];

    /** @var ?int Checkbox */
    public ?int $radio = null;

    /** @var ?string Textarea */
    public ?string $textarea = null;

    /** @var ?string POST value (saved statistics) */
    public ?string $send;
}
