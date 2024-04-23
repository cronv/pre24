<?php

namespace cronv\Task\Management\DTO;

use cronv\Task\Management\Component\AbstractJsonRequest;
use cronv\Task\Management\DTO\Trait\IdDTOTrait;

/**
 * Delete by ID DTO
 */
class DeleteIdDTO extends AbstractJsonRequest
{
    use IdDTOTrait;
}
