<?php

declare(strict_types=1);

namespace App\Tests\Integration\Task;

use App\Application\Command\CreateTaskCommand;
use App\Application\Command\DeleteTaskCommand;
use App\Domain\Exception\TaskNotFoundException;
use App\Infrastructure\Persistence\InMemory\InMemoryTaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

final class DeleteTaskTest extends KernelTestCase
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

    public function test_it_deletes_existing_task(): void
    {
        // ARRANGE
        $createCommand = new CreateTaskCommand('Test Task', 'Test Description');
        $this->commandBus->dispatch($createCommand);

        $task = $this->taskRepository->findByTitle('Test Task');
        $this->assertNotNull($task, 'Task should exist before deletion');

        $deleteCommand = new DeleteTaskCommand($task->getId());

        // ACT
        $this->commandBus->dispatch($deleteCommand);

        // ASSERT
        $deletedTask = $this->taskRepository->findById($task->getId());
        $this->assertNull($deletedTask, 'Task should not exist after deletion');
    }

    public function test_it_throws_exception_when_deleting_non_existent_task(): void
    {
        // ARRANGE
        $nonExistentId = 'non-existent-task-id';
        $command = new DeleteTaskCommand($nonExistentId);

        // ACT & ASSERT
        try {
            $this->commandBus->dispatch($command);
            $this->fail('Expected exception was not thrown');
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();
            $this->assertInstanceOf(TaskNotFoundException::class, $previous);
            $this->assertEquals('Cannot find the to be deleted task with id: non-existent-task-id.', $previous->getMessage());
        }
    }
}
