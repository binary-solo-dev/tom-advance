<?php

declare(strict_types=1);

namespace App\Tests\Integration\Task;

use App\Application\Command\CreateTaskCommand;
use App\Domain\Exception\DuplicateTitleException;
use App\Infrastructure\Persistence\InMemory\InMemoryTaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class CreateTaskTest extends KernelTestCase
{
    private InMemoryTaskRepository $taskRepository;
    private MessageBusInterface $commandBus;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->taskRepository = static::getContainer()->get(InMemoryTaskRepository::class);
        $this->commandBus = static::getContainer()->get('messenger.bus.command');
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
        try {
            $this->commandBus->dispatch($duplicateCommand);
            $this->fail('Expected exception was not thrown');
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();
            $this->assertInstanceOf(DuplicateTitleException::class, $previous);
            $this->assertEquals('Task with title "Test Task" already exists', $previous->getMessage());
        }
    }
}
