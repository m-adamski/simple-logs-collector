<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client implements UserInterface {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: "string", unique: true)]
    private string $name;

    #[Assert\NotBlank]
    #[Assert\Length(min: 12)]
    #[ORM\Column(type: "string")]
    private string $secretToken;

    #[ORM\Column(type: "boolean")]
    private bool $active;

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

    public function getUserIdentifier(): string {
        return $this->getId();
    }

    public function getRoles(): array {
        return ["ROLE_CLIENT"];
    }

    public function eraseCredentials(): void {
        // Nothing here
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getSecretToken(): string {
        return $this->secretToken;
    }

    public function setSecretToken(string $secretToken): void {
        $this->secretToken = $secretToken;
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
