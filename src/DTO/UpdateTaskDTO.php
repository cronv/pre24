<?php

namespace cronv\Task\Management\DTO;

use cronv\Task\Management\DTO\Trait\DeleteTaskDTOTrait;

/**
 * Update task DTO
 */
class UpdateTaskDTO extends TaskDTO
{
    use DeleteTaskDTOTrait;
}
