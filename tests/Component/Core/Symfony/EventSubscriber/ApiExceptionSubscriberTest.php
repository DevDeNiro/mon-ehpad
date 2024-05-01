<?php

declare(strict_types=1);

namespace Tests\Component\Core\Symfony\EventSubscriber;

use App\Core\Infrastructure\Symfony\EventSubscriber\ApiExceptionSubscriber;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Tests\Component\EventSubscriberTestCase;

#[CoversClass(ApiExceptionSubscriber::class)]
#[TestDox('Event subscriber : ' . ApiExceptionSubscriber::class)]
final class ApiExceptionSubscriberTest extends EventSubscriberTestCase
{
    protected const string EVENT_SUBSCRIBER = ApiExceptionSubscriber::class;

    #[Test]
    #[DataProvider('provideThrowable')]
    #[TestDox('$_dataName exception should update the response with the expected data and status code $statusCode')]
    public function onKernelException(\Exception $exception, int $statusCode, mixed $expectedData): void
    {
        $exceptionEvent = new ExceptionEvent(
            static::getKernel(),
            Request::create('/', Request::METHOD_GET, server: [
                'Accept' => 'application/json',
            ]),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $this->dispatch($exceptionEvent, KernelEvents::EXCEPTION);

        self::assertEquals(new JsonResponse($expectedData, $statusCode, [
            'Content-Type' => 'application/json',
        ]), $exceptionEvent->getResponse());
    }

    /**
     * @return iterable<string, array{exception: Exception, statusCode: int, expectedData: mixed}>
     */
    public static function provideThrowable(): iterable
    {
        yield 'Bad request' => [
            'exception' => new BadRequestHttpException('Bad Request'),
            'statusCode' => 400,
            'expectedData' => [
                'message' => 'Bad Request',
            ],
        ];
        yield 'Unauthorized' => [
            'exception' => new UnauthorizedHttpException('', 'Unauthorized'),
            'statusCode' => 401,
            'expectedData' => [
                'message' => 'Unauthorized',
            ],
        ];
        yield 'Access denied' => [
            'exception' => new AccessDeniedHttpException('Access Denied'),
            'statusCode' => 403,
            'expectedData' => [
                'message' => 'Access Denied',
            ],
        ];
        yield 'Not found' => [
            'exception' => new NotFoundHttpException('Not Found'),
            'statusCode' => 404,
            'expectedData' => [
                'message' => 'Not Found',
            ],
        ];
        yield 'Validation failed' => [
            'exception' => new ValidationFailedException(
                new \stdClass(),
                new ConstraintViolationList([
                    new ConstraintViolation(
                        message: 'Error',
                        messageTemplate: 'Error',
                        parameters: [],
                        root: new \stdClass(),
                        propertyPath: 'property',
                        invalidValue: null,
                    ),
                ])
            ),
            'statusCode' => 422,
            'expectedData' => [
                [
                    'propertyPath' => 'property',
                    'message' => 'Error',
                ],
            ],
        ];
        yield 'Exception' => [
            'exception' => new \Exception('Internal Server Error'),
            'statusCode' => 500,
            'expectedData' => [
                'message' => 'Internal Server Error',
            ],
        ];
    }
}
