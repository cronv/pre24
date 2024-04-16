<?php

namespace cronv\Task\Management\DTO;

use cronv\Task\Management\Component\AbstractJsonRequest;
use cronv\Task\Management\DTO\Trait\UuidDTOTrait;

/**
 * Delete task DTO
 */
class DeleteUuidDTO extends AbstractJsonRequest
{
    use UuidDTOTrait;
}
