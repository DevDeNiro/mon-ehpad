<?php

declare(strict_types=1);

namespace App\Infrastructure\Core\Symfony\HttpKernel\Exception;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

#[AutoconfigureTag('app.http_kernel.exception_mapper')]
interface ExceptionMapper
{
    public function supports(Throwable $throwable): bool;

    public function map(Throwable $throwable): JsonResponse;
}
