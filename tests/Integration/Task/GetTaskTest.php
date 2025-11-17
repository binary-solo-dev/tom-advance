<?php
declare(strict_types=1);

namespace App\Tests\Integration\Task;

use App\Application\Query\GetTaskQuery;
use App\Domain\Model\Task;
use App\Infrastructure\Persistence\InMemory\InMemoryTaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
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

}