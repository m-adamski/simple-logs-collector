<?php

namespace App\Model\DTO\Agent;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class WritePayload {
    public function __construct(

        #[Assert\NotBlank]
        #[Assert\Length(max: 250)]
        public string $measurement,

        #[Assert\NotBlank]
        public int    $level,

        #[Assert\NotBlank]
        #[Assert\Length(max: 250)]
        #[SerializedName("level_name")]
        public string $levelName,

        #[Assert\NotBlank]
        public string $message,

        public ?array $context,

        #[Assert\NotBlank]
        public int    $timestamp,
    ) {}

    public function getMeasurement(): string {
        return $this->measurement;
    }

    public function getLevel(): int {
        return $this->level;
    }

    public function getLevelName(): string {
        return $this->levelName;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function getContext(): ?array {
        return $this->context;
    }

    public function getTimestamp(): int {
        return $this->timestamp;
    }
}
