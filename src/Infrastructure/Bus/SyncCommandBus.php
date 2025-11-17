<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Application\CommandBus;

final readonly class SyncCommandBus implements CommandBus
{
    /** @var array<string, callable> */
    private array $handlers;

    public function __construct()
    {
        $this->handlers = [];
    }

    public function registerHandler(string $commandClass, callable $handler): void
    {
        $this->handlers[$commandClass] = $handler;
    }

    public function dispatch(object $command): void
    {
        $commandClass = get_class($command);
        if (!isset($this->handlers[$commandClass])) {
            throw new \RuntimeException(sprintf('No handler found for command %s', $commandClass));
        }

        $handler = $this->handlers[$commandClass];
        $handler($command);
    }
}
