<?php

namespace cronv\Task\Management\Entity\Survey;

use cronv\Task\Management\Repository\Survey\SurveyRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

#[ORM\Table(name: "survey")]
#[ORM\Entity(repositoryClass: SurveyRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Survey
{
    /** @var string UUID
     * @since https://github.com/doctrine/orm/issues/7312
     */
    #[ORM\Id]
    #[ORM\Column(name: "uuid", type: Types::GUID)]
    private string $uuid;

    /** @var string $name Name */
    #[ORM\Column(name: "name", type: Types::STRING, length: 128)]
    private string $name;

    /** @var DateTimeInterface Created */
    #[ORM\Column(name: "created_at", type: Types::DATETIME_MUTABLE, insertable: false, updatable: false)]
    private DateTimeInterface $createdAt;

    /** @var ?DateTimeInterface Updated */
    #[ORM\Column(name: "updated_at", type: Types::DATETIME_MUTABLE, insertable: false)]
    private ?DateTimeInterface $updatedAt;

    /**
     * Survey constructor.
     */
    public function __construct()
    {
        $this->uuid = UuidV4::v4();
    }

    /**
     * Get the UUID.
     *
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
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
     * Set name.
     *
     * @param string $name Name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the creation date and time of the task
     *
     * @return ?DateTimeInterface
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Get the last update date and time of the task
     *
     * @return ?DateTimeInterface
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set update at
     *
     * @return void
     */
    #[ORM\PreUpdate]
    public function setUpdateAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
