<?php
declare(strict_types=1);

namespace App\Presentation\Controller\Api;

use App\Application\Command\CreateTaskCommand;
use App\Presentation\Request\CreateTaskRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tasks', name: 'create_task', methods: ['POST'])]
final class CreateTaskController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        #[Autowire(service: 'messenger.bus.command')]
        private MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(
        #[MapRequestPayload] CreateTaskRequest $createTaskRequest
    ): JsonResponse
    {
        $command = new CreateTaskCommand(
            $createTaskRequest->title,
            $createTaskRequest->description
        );

        $taskId = $this->handle($command);

        return $this->json([
            'id' => $taskId,
            'message' => 'Task created successfully',
        ], 201);
    }
}