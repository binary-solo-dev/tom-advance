<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command\Handler;

use App\Application\Command\CreateTaskCommand;
use App\Application\Command\Handler\CreateTaskHandler;
use App\Domain\Exception\DuplicateTitleException;
use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use App\Domain\ValueObject\TaskStatus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateTaskHandlerTest extends TestCase
{
    private TaskRepositoryInterface|MockObject $taskRepository;
    private UpdateTaskHandler $updateTaskHandler;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $this->updateTaskHandler = new UpdateTaskHandler($this->taskRepository);
    }

    public function test_it_updates_an_existing_task(): void
    {
        // Arrange
        $command = new UpdateTaskCommand('existing-task-id', 'Test Task', 'Description', TaskStatus::IN_PROGRESS());

        $this->taskRepository
            ->expects($this->once())
            ->method('existsByTitle')
            ->with('Test Task')
            ->willReturn(false);

        $this->taskRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Task $task) {
                return 'Test Task' === $task->getTitle()
                    && 'Description' === $task->getDescription()
                    && TaskStatus::IN_PROGRESS() === $task->getStatus();
            }));

        // Act
        $this->updateTaskHandler->__invoke($command);
    }

    public function test_it_throws_exception_for_duplicate_title_for_existing_task(): void
    {
        // Arrange
        $command = new UpdateTaskCommand('existing-task-id', 'Duplicate Task', 'Description', TaskStatus::IN_PROGRESS());

        $this->taskRepository
            ->expects($this->once())
            ->method('existsByTitle')
            ->with('Duplicate Task')
            ->willReturn(true);

        // Act & Assert
        $this->expectException(DuplicateTitleException::class);
        $this->updateTaskHandler->__invoke($command);
    }
}
