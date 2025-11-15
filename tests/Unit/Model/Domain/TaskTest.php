<?php
declare(strict_types=1);

namespace App\Tests\Unit\Model\Domain;

use App\Domain\Model\Task;
use App\Domain\ValueObject\TaskStatus;
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{

    public function test_new_teask_starts_in_todo_status(): void
    {
        $task = new Task('My task');
        $this->assertEquals(TaskStatus::TODO(), $task->getStatus());
        $this->assertEquals('My task', $task->getTitle());
    }
}