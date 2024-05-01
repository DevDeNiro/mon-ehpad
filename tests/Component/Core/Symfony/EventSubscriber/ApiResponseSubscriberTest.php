<?php

declare(strict_types=1);

namespace Tests\Component\Core\Symfony\EventSubscriber;

use App\Core\Infrastructure\Symfony\EventSubscriber\ApiResponseSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Tests\Component\EventSubscriberTestCase;

#[CoversClass(ApiResponseSubscriber::class)]
#[TestDox('Event subscriber : ' . ApiResponseSubscriber::class)]
final class ApiResponseSubscriberTest extends EventSubscriberTestCase
{
    protected const string EVENT_SUBSCRIBER = ApiResponseSubscriber::class;

    #[Test]
    #[DataProvider('provideControllerResult')]
    #[TestDox('Controller result type $_dataName should serialize response with the expected data and status code $statusCode')]
    public function onKernelView(mixed $controllerResult, int $statusCode, mixed $expectedData): void
    {
        $viewEvent = new ViewEvent(
            static::getKernel(),
            Request::create('/', Request::METHOD_GET, server: [
                'Accept' => 'application/json',
            ]),
            HttpKernel::MAIN_REQUEST,
            $controllerResult
        );

        $this->dispatch($viewEvent, KernelEvents::VIEW);

        self::assertEquals(new JsonResponse($expectedData, $statusCode, [
            'Content-Type' => 'application/json',
        ]), $viewEvent->getResponse());
    }

    /**
     * @return iterable<string, array{controllerResult: mixed, statusCode: int, expectedData: mixed}>
     */
    public static function provideControllerResult(): iterable
    {
        yield 'int' => [
            'controllerResult' => 1,
            'statusCode' => 200,
            'expectedData' => 1,
        ];
        yield 'string' => [
            'controllerResult' => 'text',
            'statusCode' => 200,
            'expectedData' => 'text',
        ];
        yield 'float' => [
            'controllerResult' => 1.1,
            'statusCode' => 200,
            'expectedData' => 1.1,
        ];
        yield 'bool' => [
            'controllerResult' => true,
            'statusCode' => 200,
            'expectedData' => true,
        ];
        yield 'array of int' => [
            'controllerResult' => array_fill(0, 5, 1),
            'statusCode' => 200,
            'expectedData' => array_fill(0, 5, 1),
        ];
        yield 'array of string' => [
            'controllerResult' => array_fill(0, 5, 'text'),
            'statusCode' => 200,
            'expectedData' => array_fill(0, 5, 'text'),
        ];
        yield 'array of float' => [
            'controllerResult' => array_fill(0, 5, 1.1),
            'statusCode' => 200,
            'expectedData' => array_fill(0, 5, 1.1),
        ];
        yield 'array of bool' => [
            'controllerResult' => array_fill(0, 5, true),
            'statusCode' => 200,
            'expectedData' => array_fill(0, 5, true),
        ];
        yield 'object' => [
            'controllerResult' => new class() {
                public int $number = 10;
            },
            'statusCode' => 200,
            'expectedData' => [
                'number' => 10,
            ],
        ];
        yield 'array of object' => [
            'controllerResult' => array_fill(0, 5, new class() {
                public int $number = 10;
            }),
            'statusCode' => 200,
            'expectedData' => array_fill(0, 5, [
                'number' => 10,
            ]),
        ];
        yield 'null' => [
            'controllerResult' => null,
            'statusCode' => 204,
            'expectedData' => null,
        ];
    }
}
