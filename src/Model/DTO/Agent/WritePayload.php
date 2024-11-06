<?php

namespace App\Model\DTO\Agent;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class WritePayload {
    public function __construct(

        #[Assert\NotBlank]
        public string  $measurement,

        #[Assert\NotBlank]
        public int     $level,

        #[Assert\NotBlank]
        #[SerializedName("level_name")]
        public string  $levelName,

        #[Assert\NotBlank]
        public string  $message,

        public ?string $context,

        #[Assert\NotBlank]
        public int     $timestamp,
    ) {}
}
