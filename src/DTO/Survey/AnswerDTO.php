<?php

namespace cronv\Task\Management\DTO\Survey;

use cronv\Task\Management\Component\AbstractJsonRequest;
use cronv\Task\Management\DTO\Trait\IdDTOTrait;
use cronv\Task\Management\DTO\Trait\UuidDTOTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AnswerDTO DTO
 */
class AnswerDTO extends AbstractJsonRequest
{
    use IdDTOTrait, UuidDTOTrait;

    /** @var string Name */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(min: 1, max: 65535)]
    public string $name;

    /** @var string Is correct */
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    public string $isCorrect;
}
