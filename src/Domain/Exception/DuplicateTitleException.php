<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use DomainException;

final class DuplicateTitleException extends DomainException
{
    public static function becauseTaskWithTitleAlreadyExists(string $title): self
    {
        return new self(sprintf('Task with title "%s" already exists', $title));
    }
}
