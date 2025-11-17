<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command\Handler;

use App\Application\Command\CreateTaskCommand;
use App\Application\Command\Handler\CreateTaskHandler;
use App\Domain\Exception\DuplicateTitleException;
use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateTaskHandlerTest extends TestCase
{
    private TaskRepositoryInterface|MockObject $taskRepository;
    private CreateTaskHandler $handler;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $this->handler = new CreateTaskHandler($this->taskRepository);
    }

    public function test_it_creates_task(): void
    {
        // Arrange
        $command = new CreateTaskCommand('Test Task', 'Description');

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
                    && 'Description' === $task->getDescription();
            }));

        // Act
        $this->handler->__invoke($command);
    }

    public function test_it_throws_exception_for_duplicate_title(): void
    {
        // Arrange
        $command = new CreateTaskCommand('Duplicate Task');

        $this->taskRepository
            ->expects($this->once())
            ->method('existsByTitle')
            ->with('Duplicate Task')
            ->willReturn(true);

        // Act & Assert
        $this->expectException(DuplicateTitleException::class);
        $this->handler->__invoke($command);
    }
}
