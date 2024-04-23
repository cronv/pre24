<?php

namespace cronv\Task\Management\DTO\Survey;

use cronv\Task\Management\Component\AbstractJsonRequest;
use cronv\Task\Management\DTO\Trait\UuidDTOTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Survey DTO
 */
class UpdateSurveyDTO extends SurveyDTO
{
    use UuidDTOTrait;

    /** @var string Name */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 128)]
    public string $name;
}
