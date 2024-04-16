<?php

namespace cronv\Task\Management\DTO\Trait;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * UUID DTO trait
 */
trait UuidDTOTrait
{
    /** @var string UUID */
    #[Assert\Uuid]
    public string $uuid;
}
