<?php
declare(strict_types=1);

namespace App\Domain\ValueObject;

enum TaskStatus: string
{
    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';

    public static function TODO(): self { return self::TODO; }
    public static function IN_PROGRESS(): self { return self::IN_PROGRESS; }
    public static function DONE(): self { return self::DONE; }

    public function isDone(): bool { return $this === self::DONE; }
    public function isInProgress(): bool { return $this === self::IN_PROGRESS; }
}
