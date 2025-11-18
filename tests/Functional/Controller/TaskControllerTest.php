<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Domain\ValueObject\TaskStatus;
use App\Infrastructure\Persistence\InMemory\InMemoryTaskRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class TaskControllerTest extends WebTestCase
{
    private InMemoryTaskRepository $taskRepository;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        $this->taskRepository = static::getContainer()->get(InMemoryTaskRepository::class);
        $this->taskRepository->clear();
    }

    public function test_it_creates_task(): void
    {
        $this->client->request(
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