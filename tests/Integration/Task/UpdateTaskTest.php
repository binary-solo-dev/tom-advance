<?php

declare(strict_types=1);

namespace App\Tests\Integration\Task;

use App\Application\Command\CreateTaskCommand;
use App\Application\Command\UpdateTaskCommand;
use App\Domain\Exception\DuplicateTitleException;
use App\Domain\Exception\TaskNotFoundException;
use App\Domain\ValueObject\TaskStatus;
use App\Infrastructure\Persistence\InMemory\InMemoryTaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final class UpdateTaskTest extends KernelTestCase
{
    private InMemoryTaskRepository $taskRepository;
    private MessageBusInterface $commandBus;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->taskRepository = self::getContainer()->get(InMemoryTaskRepository::class);
        $this->taskRepository->clear();

        $this->commandBus = self::getContainer()->get('messenger.bus.command');
    }

    public function test_it_updates_an_existing_task(): void
    {
        // ARRANGE
        $createCommand = new CreateTaskCommand('Original Title', 'Original Description');
        $this->commandBus->dispatch($createCommand);
        $task = $this->taskRepository->findByTitle('Original Title');

        $updateCommand = new UpdateTaskCommand(
            $task->getId(),
            'Updated Title',
            'Updated Description',
            TaskStatus::IN_PROGRESS
        );

        // ACT
        $this->commandBus->dispatch($updateCommand);

        // ASSERT
        $updatedTask = $this->taskRepository->findById($task->getId());
        $this->assertEquals('Updated Title', $updatedTask->getTitle());
        $this->assertEquals('Updated Description', $updatedTask->getDescription());
        $this->assertEquals(TaskStatus::IN_PROGRESS, $updatedTask->getStatus());
    }

    public function test_it_updates_task_status_only(): void
    {
        // ARRANGE
        $createCommand = new CreateTaskCommand('Status Test Task', 'Test Description');
        $this->commandBus->dispatch($createCommand);
        $task = $this->taskRepository->findByTitle('Status Test Task');
        $originalTitle = $task->getTitle();
        $originalDescription = $task->getDescription();

        $updateCommand = new UpdateTaskCommand(
            $task->getId(),
            $originalTitle,
            $originalDescription,
            TaskStatus::IN_PROGRESS // Only changed property
        );

        // ACT
        $this->commandBus->dispatch($updateCommand);

        // ASSERT
        $updatedTask = $this->taskRepository->findById($task->getId());
        $this->assertEquals($originalTitle, $updatedTask->getTitle());
        $this->assertEquals($originalDescription, $updatedTask->getDescription());
        $this->assertEquals(TaskStatus::IN_PROGRESS, $updatedTask->getStatus());
    }

    public function test_it_throws_exception_when_updating_non_existent_task(): void
    {
        // ARRANGE
        $command = new UpdateTaskCommand(
            'non-existent-id',
            'Updated Title',
            'Updated Description',
            TaskStatus::IN_PROGRESS
        );

        // ACT & ASSERT
        try {
            $this->commandBus->dispatch($command);
            $this->fail('Expected exception was not thrown');
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();
            $this->assertInstanceOf(TaskNotFoundException::class, $previous);
            $this->assertEquals('Cannot find the task with id: non-existent-id.', $previous->getMessage());
        }
    }

    public function test_it_throws_exception_when_updating_to_existing_title(): void
    {
        // ARRANGE
        $firstCommand = new CreateTaskCommand('First Task', 'Description');
        $this->commandBus->dispatch($firstCommand);

        $secondCommand = new CreateTaskCommand('Second Task', 'Description');
        $this->commandBus->dispatch($secondCommand);
        $secondTask = $this->taskRepository->findByTitle('Second Task');

        // Try to update second task with first task's title
        $updateCommand = new UpdateTaskCommand(
            $secondTask->getId(),
            'First Task',
            'Updated Description',
            TaskStatus::IN_PROGRESS
        );

        // ACT & ASSERT
        try {
            $this->commandBus->dispatch($updateCommand);
            $this->fail('Expected exception was not thrown');
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();
            $this->assertInstanceOf(DuplicateTitleException::class, $previous);
            $this->assertEquals('Task with title "First Task" already exists', $previous->getMessage());
        }
    }

    public function test_it_allows_updating_task_with_same_title(): void
    {
        // ARRANGE
        $createCommand = new CreateTaskCommand('Same Title', 'Original Description');
        $this->commandBus->dispatch($createCommand);
        $task = $this->taskRepository->findByTitle('Same Title');

        // Then update it keeping the same title
        $updateCommand = new UpdateTaskCommand(
            $task->getId(),
            'Same Title',
            'Updated Description',
            TaskStatus::IN_PROGRESS
        );

        // ACT
        $this->commandBus->dispatch($updateCommand);

        // ASSERT
        $updatedTask = $this->taskRepository->findById($task->getId());
        $this->assertEquals('Same Title', $updatedTask->getTitle());
        $this->assertEquals('Updated Description', $updatedTask->getDescription());
        $this->assertEquals(TaskStatus::IN_PROGRESS, $updatedTask->getStatus());
    }
}