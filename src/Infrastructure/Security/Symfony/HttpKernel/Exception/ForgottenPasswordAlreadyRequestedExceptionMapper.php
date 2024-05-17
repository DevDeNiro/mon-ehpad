<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Symfony\HttpKernel\Exception;

use App\Domain\core\Validation\Assert;
use App\Domain\User\Exception\ForgottenPasswordAlreadyRequestedException;
use App\Infrastructure\Core\Symfony\HttpKernel\Exception\ExceptionMapper;
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
