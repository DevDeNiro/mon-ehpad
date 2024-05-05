<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Symfony\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\ValidationFailedException as MessengerValidationFailedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private SerializerInterface $serializer)
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

        $exceptionEvent->setResponse(
            new JsonResponse(
                $this->serializer->serialize(
                    match ($throwable::class) {
                        ValidationFailedException::class, MessengerValidationFailedException::class => (static function (ConstraintViolationListInterface $constraintViolationList): array {
                            $result = [];

                            foreach ($constraintViolationList as $violation) {
                                $result[] = [
                                    'propertyPath' => $violation->getPropertyPath(),
                                    'message' => $violation->getMessage(),
                                ];
                            }

                            return $result;
                        })($throwable->getViolations()),
                        default => [
                            'message' => $throwable->getMessage(),
                        ],
                    },
                    'json'
                ),
                match ($throwable::class) {
                    BadRequestHttpException::class => Response::HTTP_BAD_REQUEST,
                    UnauthorizedHttpException::class => Response::HTTP_UNAUTHORIZED,
                    AccessDeniedHttpException::class => Response::HTTP_FORBIDDEN,
                    NotFoundHttpException::class => Response::HTTP_NOT_FOUND,
                    ValidationFailedException::class,
                    MessengerValidationFailedException::class,
                    UnprocessableEntityHttpException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
                    default => Response::HTTP_INTERNAL_SERVER_ERROR,
                },
                [
                    'Content-Type' => 'application/json',
                ],
                true
            )
        );
    }
}
