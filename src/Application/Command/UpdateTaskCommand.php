<?php
declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\ValueObject\TaskStatus;

final readonly class UpdateTaskCommand
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $description = null,
        public TaskStatus $status,
    ) {
    }
}
