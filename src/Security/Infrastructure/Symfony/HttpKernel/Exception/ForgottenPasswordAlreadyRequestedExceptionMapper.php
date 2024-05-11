<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Symfony\HttpKernel\Exception;

use App\Core\Domain\Validation\Assert;
use App\Core\Infrastructure\Symfony\HttpKernel\Exception\ExceptionMapper;
use App\Security\Domain\Model\Exception\ForgottenPasswordAlreadyRequestedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final readonly class ForgottenPasswordAlreadyRequestedExceptionMapper implements ExceptionMapper
{
    public function supports(Throwable $throwable): bool
    {
        return $throwable->getPrevious() instanceof ForgottenPasswordAlreadyRequestedException;
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
