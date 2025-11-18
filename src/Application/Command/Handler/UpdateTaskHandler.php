<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\UpdateTaskCommand;
use App\Domain\Exception\DuplicateTitleException;
use App\Domain\Exception\StatusException;
use App\Domain\Exception\TaskNotFoundException;
use App\Domain\Repository\TaskRepositoryInterface;

final class UpdateTaskHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
    ) {
    }

    /**
     * @throws StatusException
     * @throws TaskNotFoundException
     */
    public function __invoke(UpdateTaskCommand $command): void
    {
        $task = $this->taskRepository->findById($command->id);

        if ($task === null) {
            throw TaskNotFoundException::becauseTaskWithIdDoesNotExist($command->id);
        }

        // Check for duplicate title only if title is being changed
        if ($task->getTitle() !== $command->title && $this->taskRepository->existsByTitle($command->title)) {
            throw DuplicateTitleException::becauseTaskWithTitleAlreadyExists($command->title);
        }

        $task->setTitle($command->title);
        $task->setDescription($command->description);
        $task->setUpdatedAt(new \DateTimeImmutable());

        $task->changeStatus($command->status);

        $this->taskRepository->save($task);
    }
}
