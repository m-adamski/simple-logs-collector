<?php

namespace App\Model\Event;

use App\Entity\Client;

class Event {
    private ?Client $client = null;
    private string $measurement;
    private int $level;
    private string $levelName;
    private string $message;
    private ?array $context = null;
    private \DateTime $dateTime;

    public function getClient(): ?Client {
        return $this->client;
    }

    public function setClient(?Client $client): Event {
        $this->client = $client;
        return $this;
    }

    public function getMeasurement(): string {
        return $this->measurement;
    }

    public function setMeasurement(string $measurement): Event {
        $this->measurement = $measurement;
        return $this;
    }

    public function getLevel(): int {
        return $this->level;
    }

    public function setLevel(int $level): Event {
        $this->level = $level;
        return $this;
    }

    public function getLevelName(): string {
        return $this->levelName;
    }

    public function setLevelName(string $levelName): Event {
        $this->levelName = $levelName;
        return $this;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function setMessage(string $message): Event {
        $this->message = $message;
        return $this;
    }

    public function getContext(): ?array {
        return $this->context;
    }

    public function setContext(?array $context): Event {
        $this->context = $context;
        return $this;
    }

    public function getDateTime(): \DateTime {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): Event {
        $this->dateTime = $dateTime;
        return $this;
    }
}
