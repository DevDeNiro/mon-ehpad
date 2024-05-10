<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Symfony\HttpKernel\Exception;

use App\Core\Domain\Validation\Assert;
use App\Core\Infrastructure\Symfony\HttpKernel\Exception\ExceptionMapper;
use App\Security\Domain\Model\Exception\InvalidStateException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class InvalidStateExceptionMapper implements ExceptionMapper
{
    public function supports(Throwable $throwable): bool
    {
        return $throwable->getPrevious() instanceof InvalidStateException;
    }

    public function map(Throwable $throwable): JsonResponse
    {
        Assert::notNull($throwable->getPrevious());

        return new JsonResponse(
            [
                'message' => $throwable->getPrevious()->getMessage(),
            ],
            Response::HTTP_FORBIDDEN,
            [
                'Content-Type' => 'application/json',
            ],
        );
    }
}
