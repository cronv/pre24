<?php

namespace cronv\Task\Management\DTO\Survey;

use cronv\Task\Management\Component\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Survey DTO
 */
class SurveyDTO extends AbstractJsonRequest
{
    /** @var string Name */
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(min: 3, max: 128)]
    public string $name;
}
