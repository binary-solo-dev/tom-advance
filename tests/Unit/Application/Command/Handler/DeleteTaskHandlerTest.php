<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command\Handler;

use App\Domain\Exception\TaskNotFoundException;
use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteTaskHandlerTest extends TestCase
{
    private TaskRepositoryInterface|MockObject $taskRepository;
    private DeleteTaskHandler $deleteTaskHandler;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $this->deleteTaskHandler = new DeleteTaskHandler($this->taskRepository);
    }

    public function test_it_deletes_an_existing_task(): void
    {
        // ARRANGE
        $existingTask = new Task('Test Task', 'Description');
        $taskId = $existingTask->getId();

        $command = new DeleteTaskCommand($taskId);

        // ACT
        $this->taskRepository
            ->expects($this->once())
            ->method('findById')
            ->with($taskId)
            ->willReturn($existingTask);

        $this->taskRepository
            ->expects($this->once())
            ->method('delete')
            ->with($existingTask);
        // ASSERT
        $this->deleteTaskHandler->__invoke($command);
    }

    public function test_it_throws_exception_when_a_task_is_not_found(): void
    {
        // ARRANGE
        $nonExistingTaskId = 'non-existent-task-id';

        $command = new DeleteTaskCommand($nonExistingTaskId);

        $this->taskRepository
            ->expects($this->once())
            ->method('findById')
            ->with($nonExistingTaskId)
            ->willReturn(null);

        $this->taskRepository
            ->expects($this->never())
            ->method('delete');

        // ACT & ASSERT
        $this->expectException(TaskNotFoundException::class);
        $this->expectExceptionMessage('Cannot find the to be deleted task with id: non-existent-task-id.');
        $this->updateTaskHandler->__invoke($command);
    }
}
