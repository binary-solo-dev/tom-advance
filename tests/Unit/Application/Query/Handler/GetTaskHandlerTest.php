<?php
declare(strict_types=1);

namespace App\Tests\Unit\Application\Command\Handler;

use App\Application\Query\GetTaskQuery;
use App\Application\Query\Handler\GetTaskHandler;
use App\Domain\Exception\TaskNotFoundException;
use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetTaskHandlerTest extends TestCase
{
    private TaskRepositoryInterface|MockObject $taskRepository;
    private GetTaskHandler $getTaskHandler;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $this->getTaskHandler = new GetTaskHandler($this->taskRepository);
    }

    public function test_it_returns_task_when_it_exists(): void
    {
        // ARRANGE
        $task = new Task('Test Task', 'Description');
        $taskId = $task->getId();

        $this->taskRepository
            ->expects($this->once())
            ->method('findById')
            ->with($taskId)
            ->willReturn($task);

        $query = new GetTaskQuery($taskId);

        // ACT
        $result = $this->getTaskHandler->__invoke($query);

        // ASSERT
        $this->assertSame($task, $result);
    }

    public function test_it_throws_task_not_found_exception_when_task_not_found(): void
    {
        // ARRANGE
        $taskId = 'non-existent-task-id';
        $this->taskRepository
            ->expects($this->once())
            ->method('findById')
            ->with($taskId)
            ->willReturn(null);

        $query = new GetTaskQuery($taskId);

        // ACT & ASSERT
        $this->expectException(TaskNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Cannot find the task with id: %s', $taskId));
        $this->getTaskHandler->__invoke($query);
    }
}