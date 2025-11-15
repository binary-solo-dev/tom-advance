<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\Domain;

use App\Domain\Exception\StatusException;
use App\Domain\Model\Task;
use App\Domain\ValueObject\TaskStatus;
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{
    public function test_new_task_starts_in_todo_status(): void
    {
        $task = new Task('My task');
        self::assertEquals(TaskStatus::TODO(), $task->getStatus());
        self::assertEquals('My task', $task->getTitle());
        self::assertNotNull($task->getId());
        self::assertNull($task->getDescription());
        self::assertNotNull($task->getCreatedAt());
        self::assertNotNull($task->getUpdatedAt()); 
    }

    public function test_can_create_task_with_title_and_description(): void
    {
        $task = new Task('My task', 'Task description');
        self::assertEquals('My task', $task->getTitle());
        self::assertEquals('Task description', $task->getDescription());
        self::assertEquals(TaskStatus::TODO(), $task->getStatus());
    }

    public function test_it_throws_status_exception_trying_to_change_status_to_done_when_not_in_progress(): void
    {
        $task = new Task('My task');
        $this->expectException(StatusException::class);
        $this->expectExceptionMessage('Task must be in progress before being marked done.');
        $task->changeStatus(TaskStatus::DONE);
        $this->assertEquals(TaskStatus::TODO(), $task->getStatus());
    }

    public function test_it_throws_status_exception_when_changing_status_of_done_task(): void
    {
        $task = new Task('My task');
        $task->changeStatus(TaskStatus::IN_PROGRESS);
        $task->changeStatus(TaskStatus::DONE);

        $this->expectException(StatusException::class);
        $this->expectExceptionMessage('Cannot change status of a done task.');
        $task->changeStatus(TaskStatus::IN_PROGRESS);
    }
}