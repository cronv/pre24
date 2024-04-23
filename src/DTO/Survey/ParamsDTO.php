<?php

namespace cronv\Task\Management\DTO\Survey;

use cronv\Task\Management\Component\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ListParams DTO
 */
class ParamsDTO extends AbstractJsonRequest
{
    /** @var ?int ID */
    public ?int $id;

    /** @var ?string UUID */
    #[Assert\Uuid]
    public ?string $uuid;

    /** @var ?string Name */
    public ?string $name;

    /** @var ?int Номер страницы */
    public ?int $page;

    /** @var ?int User ID */
    public ?int $userId;
}
