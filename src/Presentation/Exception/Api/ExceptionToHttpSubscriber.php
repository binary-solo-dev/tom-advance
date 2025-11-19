<?php
declare(strict_types=1);

namespace App\Presentation\Exception\Api;

use App\Domain\Exception\DuplicateTitleException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class ExceptionToHttpSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // If it's a messenger exception, unwrap it
        if ($exception instanceof HandlerFailedException) {
            foreach ($exception->getWrappedExceptions() as $nested) {
                $exception = $nested;
                break;
            }
        }

        // Handle domain exception
        if ($exception instanceof DuplicateTitleException) {
            $event->setResponse(new JsonResponse(
                ['error' => 'DuplicateTitleException', 'message' => $exception->getMessage()],
                409
            ));
            return;
        }
    }
}