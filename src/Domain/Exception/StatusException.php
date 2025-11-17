<?php
declare(strict_types=1);

namespace App\Domain\Exception;

final class StatusException extends \Exception
{
    public static function becauseTaskAlreadyHasTheStatusDone(): self
    {
        return new self('Cannot change status of a done task.');
    }

    public static function becauseTaskCannotBeDeletedWhenDone(): self
    {
        return new self('Cannot delete a task that is done.');
    }
    public static function becauseTaskCannotFinishWhenNotInProgress(): self
    {
        return new self('Task must be in progress before being marked done.');
    }
}