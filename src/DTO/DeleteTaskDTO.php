<?php

namespace cronv\Task\Management\DTO;

use cronv\Task\Management\Component\AbstractJsonRequest;
use cronv\Task\Management\DTO\Trait\DeleteTaskDTOTrait;

/**
 * Delete task DTO
 */
class DeleteTaskDTO extends AbstractJsonRequest
{
    use DeleteTaskDTOTrait;
}
