<?php
declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\DeleteTaskCommand;
use App\Domain\Exception\TaskNotFoundException;
use App\Domain\Repository\TaskRepositoryInterface;

final class DeleteTaskHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
    ) {
    }

    /**
     * @throws TaskNotFoundException
     */
    public function __invoke(DeleteTaskCommand $taskQuery): void
    {
        $task = $this->taskRepository->findById($taskQuery->id);

        if (null === $task) {
            throw TaskNotFoundException::becauseToBeDeletedTaskWithIdDoesNotExist($taskQuery->id);
        }

        $this->taskRepository->delete($task);
    }
}