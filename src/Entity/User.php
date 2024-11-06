<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: "string")]
    private string $name;

    #[Assert\Email]
    #[ORM\Column(type: "string", unique: true)]
    private string $emailAddress;

    private ?string $plainPassword = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $password = null;

    #[ORM\Column(type: "json")]
    private array $roles = ["ROLE_USER"];

    #[ORM\Column(type: "boolean")]
    private bool $active = true;

    #[ORM\Column(type: "datetime")]
    private \DateTime $creationDate;

    #[ORM\Column(type: "datetime")]
    private \DateTime $updateDate;

    public function __construct() {
        $this->creationDate = new \DateTime();
        $this->updateDate = new \DateTime();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getEmailAddress(): string {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): void {
        $this->emailAddress = $emailAddress;
    }

    public function getUserIdentifier(): string {
        return $this->getEmailAddress();
    }

    public function getPlainPassword(): ?string {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void {
        $this->plainPassword = $plainPassword;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(?string $password): void {
        $this->password = $password;
    }

    public function eraseCredentials(): void {
        // Nothing here
    }

    public function getRoles(): array {
        return array_unique(array_merge($this->roles, ["ROLE_USER"]));
    }

    public function setRoles(array $roles): void {
        $this->roles = array_unique(array_merge($roles, ["ROLE_USER"]));
    }

    public function isActive(): bool {
        return $this->active;
    }

    public function setActive(bool $active): void {
        $this->active = $active;
    }

    public function getCreationDate(): \DateTime {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTime $creationDate): void {
        $this->creationDate = $creationDate;
    }

    public function getUpdateDate(): \DateTime {
        return $this->updateDate;
    }

    public function setUpdateDate(\DateTime $updateDate): void {
        $this->updateDate = $updateDate;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void {
        $this->updateDate = new \DateTime();
    }
}
