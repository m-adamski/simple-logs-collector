<?php

namespace App\Entity;

use App\Model\Event\Level;
use App\Model\Filter\QuickRange;
use App\Repository\FilterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: FilterRepository::class)]
class Filter {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "filters")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(name: "client_id", referencedColumnName: "id", nullable: true)]
    private ?Client $client = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $measurement = null;

    #[ORM\Column(type: "integer", nullable: true, enumType: Level::class)]
    private ?Level $level = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTime $startDate = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTime $endDate = null;

    #[ORM\Column(type: "string", nullable: true, enumType: QuickRange::class)]
    private ?QuickRange $quickRange = null;

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

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): void {
        $this->user = $user;
    }

    public function getClient(): ?Client {
        return $this->client;
    }

    public function setClient(?Client $client): void {
        $this->client = $client;
    }

    public function getMeasurement(): ?string {
        return $this->measurement;
    }

    public function setMeasurement(?string $measurement): void {
        $this->measurement = $measurement;
    }

    public function getLevel(): ?Level {
        return $this->level;
    }

    public function setLevel(?Level $level): void {
        $this->level = $level;
    }

    public function getStartDate(): ?\DateTime {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): void {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTime {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): void {
        $this->endDate = $endDate;
    }

    public function getQuickRange(): ?QuickRange {
        return $this->quickRange;
    }

    public function setQuickRange(?QuickRange $quickRange): void {
        $this->quickRange = $quickRange;
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
