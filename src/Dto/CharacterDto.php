<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CharacterDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 500)]
        public readonly string $name,

        #[Assert\NotBlank]
        #[Assert\Positive]
        public readonly int $externalId,
    ) {
    }
}