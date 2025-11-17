<?php
declare(strict_types=1);

namespace App\Application\Query\Handler;

use App\Application\Query\GetTaskQuery;
use App\Domain\Exception\TaskNotFoundException;
use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;

final class GetTaskHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
    ) {
    }

    public function __invoke(GetTaskQuery $taskQuery): Task
    {
        $task = $this->taskRepository->findById($taskQuery->id);

        if (null === $task) {
            throw TaskNotFoundException::becauseTaskWithIdDoesNotExist($taskQuery->id);
        }

        return $task;
    }
}