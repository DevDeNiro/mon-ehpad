<?php

declare(strict_types=1);

namespace App\Infrastructure\Core\Symfony\HttpKernel\Exception;

use App\Domain\core\Validation\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Webmozart\Assert\InvalidArgumentException;

final class InvalidArgumentExceptionMapper implements ExceptionMapper
{
    public function supports(Throwable $throwable): bool
    {
        return $throwable->getPrevious() instanceof InvalidArgumentException;
    }

    public function map(Throwable $throwable): JsonResponse
    {
        Assert::notNull($throwable->getPrevious());

        return new JsonResponse(
            [
                'message' => $throwable->getPrevious()->getMessage(),
            ],
            Response::HTTP_INTERNAL_SERVER_ERROR,
            [
                'Content-Type' => 'application/json',
            ],
        );
    }
}
