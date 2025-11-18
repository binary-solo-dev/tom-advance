<?php
declare(strict_types=1);

namespace App\Tests\Integration\Task;

use App\Application\Query\GetTaskQuery;
use App\Domain\Exception\DuplicateTitleException;
use App\Domain\Exception\TaskNotFoundException;
use App\Domain\Model\Task;
use App\Infrastructure\Persistence\InMemory\InMemoryTaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class GetTaskTest extends KernelTestCase
{
    private InMemoryTaskRepository $taskRepository;
    private MessageBusInterface $queryBus;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->taskRepository = static::getContainer()->get(InMemoryTaskRepository::class);
        $this->queryBus = static::getContainer()->get('messenger.bus.query');
    }

    public function test_it_can_retrieve_an_existing_task(): void
    {
        // ARRANGE
        $task = new Task('Test task', 'Test description');
        $this->taskRepository->save($task);

        $query = new GetTaskQuery($task->getId());

        // ACT
        $envelope = $this->queryBus->dispatch($query);
        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);
        $result = $handledStamp->getResult();

        // ASSERT
        $this->assertEquals($task->getId(), $result->getId());
        $this->assertEquals('Test task', $result->getTitle());
        $this->assertEquals('Test description', $result->getDescription());
    }

    public function test_it_a_not_found_exception_is_thrown_when_a_task_does_not_exist(): void
    {
        // ARRANGE
        $query = new GetTaskQuery('non-existent-task-id');

        // ACT & ASSERT
        try {
            $this->queryBus->dispatch($query);
            $this->fail('Expected exception was not thrown');
        } catch (HandlerFailedException $e) {
            $previous = $e->getPrevious();
            $this->assertInstanceOf(TaskNotFoundException::class, $previous);
            $this->assertEquals('Cannot find the task with id: non-existent-task-id.', $previous->getMessage());
        }
    }
}