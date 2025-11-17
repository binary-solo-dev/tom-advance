<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\InMemory;

use App\Domain\Exception\DuplicateTitleException;
use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;

final class InMemoryTaskRepository implements TaskRepositoryInterface
{
    /** @var array<string, Task> */
    private array $tasks = [];

    public function save(Task $task): void
    {
        if ($this->existsByTitle($task->getTitle())) {
            throw DuplicateTitleException::becauseTaskWithTitleAlreadyExists($task->getTitle());
        }

        $this->tasks[$task->getId()] = $task;
    }

    public function existsByTitle(string $title): bool
    {
        foreach ($this->tasks as $existingTask) {
            if ($existingTask->getTitle() === $title) {
                return true;
            }
        }

        return false;
    }

    public function findById(string $id): ?Task
    {
        return $this->tasks[$id] ?? null;
    }

    public function findAll(): array
    {
        return array_values($this->tasks);
    }

    public function findByTitle(string $title): ?Task
    {
        foreach ($this->tasks as $task) {
            if ($task->getTitle() === $title) {
                return $task;
            }
        }

        return null;
    }

    public function delete(Task $task): void
    {
        unset($this->tasks[$task->getId()]);
    }

    public function clear(): void
    {
        $this->tasks = [];
    }
}