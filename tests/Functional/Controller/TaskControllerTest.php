<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Domain\ValueObject\TaskStatus;
use App\Infrastructure\Persistence\InMemory\InMemoryTaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class TaskControllerTest extends WebTestCase
{
    private InMemoryTaskRepository $taskRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskRepository = static::getContainer()->get(InMemoryTaskRepository::class);
        $this->taskRepository->clear();
    }

    // CREATE task tests
    public function test_it_creates_task(): void
    {
        $client = self::createClient();

        $client->request(
            'POST',
            '/tasks',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'title' => 'Test Task',
                'description' => 'Test Description'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $task = $this->taskRepository->findByTitle('Test Task');
        $this->assertNotNull($task);
        $this->assertEquals('Test Description', $task->getDescription());
        $this->assertEquals(TaskStatus::TODO, $task->getStatus());
    }
}