<?php
declare(strict_types=1);

namespace App\Domain\Exception;

final class TaskNotFoundException extends \Exception
{
    public static function becauseTaskWithIdDoesNotExist(string $taskId): self
    {
        return new self(sprintf('Cannot find the task with id: %s.', $taskId));
    }
}