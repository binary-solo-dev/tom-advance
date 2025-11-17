<?php

declare(strict_types=1);

namespace App\Tests\Integration\Task;

use App\Application\Command\CreateTaskCommand;
use App\Application\Command\Handler\CreateTaskHandler;
use App\Domain\Exception\DuplicateTitleException;
use App\Infrastructure\Bus\SyncCommandBus;
use App\Infrastructure\Persistence\InMemory\InMemoryTaskRepository;
use PHPUnit\Framework\TestCase;

final class CreateTaskTest extends TestCase
{
    private SyncCommandBus $commandBus;
    private InMemoryTaskRepository $taskRepository;

    protected function setUp(): void
    {
        $this->taskRepository = new InMemoryTaskRepository();
        $this->commandBus = new SyncCommandBus();

        $createTaskHandler = new CreateTaskHandler($this->taskRepository);
        $this->commandBus->registerHandler(
            CreateTaskCommand::class,
            $createTaskHandler
        );
    }

    public function test_it_creates_task(): void
    {
        // Arrange
        $command = new CreateTaskCommand('Test Task', 'Test Description');

        // Act
        $this->commandBus->dispatch($command);

        // Assert
        $task = $this->taskRepository->findByTitle('Test Task');

        $this->assertNotNull($task);
        $this->assertEquals('Test Task', $task->getTitle());
        $this->assertEquals('Test Description', $task->getDescription());
    }

    public function test_it_cannot_create_task_with_duplicate_title(): void
    {
        // Arrange
        $command = new CreateTaskCommand('Test Task');
        $this->commandBus->dispatch($command);

        $duplicateCommand = new CreateTaskCommand('Test Task');

        // Act & Assert
        $this->expectException(DuplicateTitleException::class);
        $this->commandBus->dispatch($duplicateCommand);
    }
}
