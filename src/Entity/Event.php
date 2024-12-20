<?php

namespace App\Entity;

use App\Model\Event\Level;
use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event {
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: "client_id", referencedColumnName: "id")]
    private Client $client;

    #[ORM\Column(type: "string")]
    private string $measurement;

    #[ORM\Column(type: "integer", enumType: Level::class)]
    private Level $level;

    #[ORM\Column(type: "text")]
    private string $message;

    #[ORM\Column(type: "json", nullable: true)]
    private ?array $context = null;

    #[ORM\Column(type: "datetime")]
    private \DateTime $timestamp;

    #[ORM\Column(type: "datetime")]
    private \DateTime $creationDate;

    #[ORM\Column(type: "datetime")]
    private \DateTime $updateDate;

    public function __construct() {
        $this->creationDate = new \DateTime();
        $this->updateDate = new \DateTime();
    }

    public function getId(): ?Uuid {
        return $this->id;
    }

    public function getClient(): Client {
        return $this->client;
    }

    public function setClient(Client $client): void {
        $this->client = $client;
    }

    public function getMeasurement(): string {
        return $this->measurement;
    }

    public function setMeasurement(string $measurement): void {
        $this->measurement = $measurement;
    }

    public function getLevel(): Level {
        return $this->level;
    }

    public function setLevel(Level $level): void {
        $this->level = $level;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function setMessage(string $message): void {
        $this->message = $message;
    }

    public function getContext(): ?array {
        return $this->context;
    }

    public function setContext(?array $context): void {
        $this->context = $context;
    }

    public function getTimestamp(): \DateTime {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTime $timestamp): void {
        $this->timestamp = $timestamp;
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
