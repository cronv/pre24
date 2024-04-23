<?php

namespace cronv\Task\Management\DTO\Survey;

use cronv\Task\Management\Component\AbstractJsonRequest;
use cronv\Task\Management\DTO\Trait\IdDTOTrait;
use cronv\Task\Management\DTO\Trait\UuidDTOTrait;

/**
 * Delete by ID DTO
 */
class DeleteAssigmentDTO extends AbstractJsonRequest
{
    use IdDTOTrait, UuidDTOTrait;
}
