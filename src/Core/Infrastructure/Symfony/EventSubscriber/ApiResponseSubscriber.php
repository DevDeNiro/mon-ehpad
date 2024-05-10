<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Symfony\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class ApiResponseSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SerializerInterface $serializer
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => 'onKernelView',
        ];
    }

    public function onKernelView(ViewEvent $viewEvent): void
    {
        $result = $viewEvent->getControllerResult();

        if ($result === null) {
            $viewEvent->setResponse(new JsonResponse(status: Response::HTTP_NO_CONTENT));

            return;
        }

        $viewEvent->setResponse(
            new JsonResponse(
                $this->serializer->serialize($result, 'json'),
                Response::HTTP_OK,
                [],
                true
            )
        );
    }
}
