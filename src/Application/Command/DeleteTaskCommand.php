<?php
declare(strict_types=1);

namespace App\Application\Command;

final readonly class DeleteTaskCommand
{
    public function __construct(
        public readonly string $id
    ) {
    }
}