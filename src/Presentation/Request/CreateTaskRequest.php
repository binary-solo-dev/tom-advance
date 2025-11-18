<?php

declare(strict_types=1);

namespace App\Presentation\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateTaskRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public readonly string $title,

        #[Assert\Length(max: 1000)]
        public readonly ?string $description = null,
    ) {
    }
}
