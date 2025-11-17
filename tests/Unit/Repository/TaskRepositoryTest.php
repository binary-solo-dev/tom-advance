<?php
declare(strict_types=1);

namespace App\Tests\Unit\Repository;

use App\Domain\Exception\DuplicateTitleException;
use App\Domain\Model\Task;
use App\Infrastructure\Persistence\InMemory\InMemoryTaskRepository;
use PHPUnit\Framework\TestCase;

final class TaskRepositoryTest extends TestCase
{
    private InMemoryTaskRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new InMemoryTaskRepository();
    }

    public function test_cannot_save_task_with_duplicate_title(): void
    {
        $task1 = new Task('Unique Title');
        $this->repository->save($task1);

        $task2 = new Task('Unique Title');

        $this->expectException(DuplicateTitleException::class);
        $this->expectExceptionMessage('Task with title "Unique Title" already exists');

        $this->repository->save($task2);
    }
}