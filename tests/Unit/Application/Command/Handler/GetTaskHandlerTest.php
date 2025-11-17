<?php
declare(strict_types=1);

namespace App\Tests\Unit\Application\Command\Handler;

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

}