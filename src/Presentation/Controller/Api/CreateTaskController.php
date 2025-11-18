<?php
declare(strict_types=1);

namespace App\Presentation\Controller\Api;

use App\Application\Command\CreateTaskCommand;
use App\Presentation\Request\CreateTaskRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tasks', name: 'create_task', methods: ['POST'])]
final class CreateTaskController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus
    ) {
    }

    public function __invoke(CreateTaskRequest $createTaskRequest): JsonResponse
    {
        $command = new CreateTaskCommand(
            $createTaskRequest->title,
            $createTaskRequest->description
        );

        $taskId = $this->commandBus->dispatch($command);

        return $this->json([
            'id' => $taskId,
            'message' => 'Task created successfully',
        ], 201);
    }
}