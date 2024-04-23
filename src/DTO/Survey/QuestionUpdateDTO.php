<?php

namespace cronv\Task\Management\DTO\Survey;

use cronv\Task\Management\DTO\Trait\IdDTOTrait;
use cronv\Task\Management\DTO\Trait\UuidDTOTrait;

/**
 * QuestionUpdate DTO
 */
class QuestionUpdateDTO extends QuestionDTO
{
    use UuidDTOTrait, IdDTOTrait;
}
