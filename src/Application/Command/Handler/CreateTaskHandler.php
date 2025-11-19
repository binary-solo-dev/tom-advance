<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\CreateTaskCommand;
use App\Domain\Exception\DuplicateTitleException;
use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;

final class CreateTaskHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
    ) {
    }

    /**
     * @throws DuplicateTitleException
     */
    public function __invoke(CreateTaskCommand $command): void
    {
        $task = new Task($command->title, $command->description);

        if ($this->taskRepository->existsByTitle($task->getTitle())) {
            throw DuplicateTitleException::becauseTaskWithTitleAlreadyExists($task->getTitle());
        }

        $this->taskRepository->save($task);
    }
}
