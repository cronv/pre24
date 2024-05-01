<?php

namespace cronv\Task\Management\Entity\Survey;

use cronv\Task\Management\Repository\Survey\SurveyMappingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV4;

#[ORM\Table(name: "survey_mapping")]
#[ORM\Entity(repositoryClass: SurveyMappingRepository::class)]
class SurveyMapping
{
    /** @var int Id */
    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;

    /** @var string survey_assignment.uuid_new */
    #[ORM\Column(name: "uuid_new", type: Types::GUID)]
    private string $uuidNew;

    /**
     * UuidResults constructor.
     *
     * @param int $userId users.id
     * @param string $uuid survey_assignment.uuid
     */
    public function __construct(
        #[ORM\Column(name: "uuid", type: Types::GUID)]
        private readonly string $uuid,

        #[ORM\Column(name: "user_id", type: Types::INTEGER)]
        private readonly int $userId
    )
    {
        $this->uuidNew = UuidV4::v4();
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
     * Get the UUID New.
     *
     * @return string
     */
    public function getUuidNew(): string
    {
        return $this->uuidNew;
    }
}
