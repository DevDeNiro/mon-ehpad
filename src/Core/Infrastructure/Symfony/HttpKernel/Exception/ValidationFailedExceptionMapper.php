<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Symfony\HttpKernel\Exception;

use App\Core\Domain\Validation\Assert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\Exception\ValidationFailedException as MessengerValidationFailedException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

final class ValidationFailedExceptionMapper implements ExceptionMapper
{
    public function supports(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationFailedException
            || $throwable instanceof MessengerValidationFailedException;
    }

    public function map(Throwable $throwable): JsonResponse
    {
        Assert::isInstanceOfAny(
            $throwable,
            [
                ValidationFailedException::class,
                MessengerValidationFailedException::class
            ]
        );

        /** @var ConstraintViolation[] $violations */
        $violations = iterator_to_array($throwable->getViolations());

        return new JsonResponse(
            array_map(
                static function (ConstraintViolation $violation): array {
                    return [
                        'propertyPath' => $violation->getPropertyPath(),
                        'message' => $violation->getMessage(),
                    ];
                },
                $violations,
            ),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            [
                'Content-Type' => 'application/json',
            ],
        );
    }
}
