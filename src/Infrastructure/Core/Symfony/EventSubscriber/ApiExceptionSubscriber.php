<?php

declare(strict_types=1);

namespace App\Infrastructure\Core\Symfony\EventSubscriber;

use App\Infrastructure\Core\Symfony\HttpKernel\Exception\ExceptionMapper;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class ApiExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @param iterable<ExceptionMapper> $exceptionMappers
     */
    public function __construct(
        #[TaggedIterator('app.http_kernel.exception_mapper')]
        private iterable $exceptionMappers
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $exceptionEvent): void
    {
        $throwable = $exceptionEvent->getThrowable();

        foreach ($this->exceptionMappers as $exceptionMapper) {
            if ($exceptionMapper->supports($throwable)) {
                $exceptionEvent->setResponse($exceptionMapper->map($throwable));
                return;
            }
        }

        $exceptionEvent->setResponse(new JsonResponse($throwable->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR));
    }
}
