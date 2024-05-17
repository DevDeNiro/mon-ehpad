<?php

declare(strict_types=1);

namespace App\Infrastructure\Core\Symfony\HttpKernel\Exception;

use App\Domain\core\Validation\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

final class HttpExceptionMapper implements ExceptionMapper
{
    public function supports(Throwable $throwable): bool
    {
        return $throwable instanceof HttpException;
    }

    public function map(Throwable $throwable): JsonResponse
    {
        Assert::isInstanceOf($throwable, HttpException::class);

        return new JsonResponse(
            [
                'message' => $throwable->getMessage(),
            ],
            $throwable->getStatusCode(),
            [
                'Content-Type' => 'application/json',
            ],
        );
    }
}
