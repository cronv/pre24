<?php

namespace cronv\Task\Management\DTO\Trait;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Delete task DTO trait
 */
trait DeleteTaskDTOTrait
{
    /** @var string UUID task */
    #[Assert\Uuid]
    public string $uuid;
}
