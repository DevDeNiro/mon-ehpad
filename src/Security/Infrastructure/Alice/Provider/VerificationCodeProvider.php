<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Alice\Provider;

use App\Core\Infrastructure\Alice\Provider\Provider;

final readonly class VerificationCodeProvider implements Provider
{
    public static function otp(string $current): string
    {
        return sprintf('%06d', (int) $current);
    }
}
