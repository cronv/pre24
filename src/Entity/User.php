<?php

namespace cronv\Task\Management\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use cronv\Task\Management\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Entity: User
 */
#[ORM\Table(name: "users")]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /** @var int Identity key */
    #[ORM\Id]
    #[ORM\Column(name: "id", type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    private int $id;

    /** @var string Login user */
    #[ORM\Column(name: "username", type: Types::STRING, length: 180, unique: true)]
    private string $username;

    /** @var array Roles (access) */
    #[ORM\Column(name: "roles", type: Types::JSON)]
    private array $roles = [];

    /** @var string The hashed password */
    #[ORM\Column(name: "password", type: Types::STRING)]
    private string $password;

    /**
     * Get the value of id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Set the value of username.
     *
     * @param string $username
     * @return static
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the User identifier.
     *
     * This method is required by the UserInterface.
     *
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * Get the roles of the user.
     *
     * This method is required by the UserInterface.
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Set the roles of the user.
     *
     * @param array $roles
     * @return static
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the hashed password of the user.
     *
     * This method is required by the PasswordAuthenticatedUserInterface.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the hashed password of the user.
     *
     * @param string $password The hashed password to set.
     * @return static
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Erase credentials of the user.
     *
     * This method is required by the UserInterface. It can be used to clear any sensitive data once the password has been hashed.
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
