<?php

namespace cronv\Task\Management\DTO\Survey;

use cronv\Task\Management\Component\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AssignmentDTO DTO
 */
class AssignmentDTO extends AbstractJsonRequest
{
    /** @var int User ID */
    #[Assert\NotBlank]
    #[Assert\Type('int')]
    public int $userId;

    /** @var string Survey ID */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $surveyId;

    /** @var ?int Attempts */
    #[Assert\Type('int')]
    public ?int $attempts;

    /** @var ?string Access */
    #[Assert\Type('int')]
    public ?string $access;

    /** @var ?string Started at */
    #[Assert\DateTime(format: 'Y-m-d\TH:i')]
    public ?string $startedAt;

    /** @var ?string Ended at */
    #[Assert\DateTime(format: 'Y-m-d\TH:i')]
    public ?string $endedAt;
}
