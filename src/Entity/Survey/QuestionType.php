<?php

namespace cronv\Task\Management\Entity\Survey;

use cronv\Task\Management\Repository\Survey\QuestionTypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "question_type")]
#[ORM\Entity(repositoryClass: QuestionTypeRepository::class)]
class QuestionType
{
    /** @var int ID */
    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::BIGINT)]
    private int $id;

    /** @var string Name */
    #[ORM\Column(name: "name", type: Types::STRING, length: 32)]
    private string $name;

    /** @var string Description */
    #[ORM\Column(name: "description", type: Types::STRING, length: 64)]
    private string $description;

    /**
     * Get the ID.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
