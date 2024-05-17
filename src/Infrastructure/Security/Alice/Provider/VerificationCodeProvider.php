<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Alice\Provider;

use App\Infrastructure\Core\Alice\Provider\Provider;

final readonly class VerificationCodeProvider implements Provider
{
    public static function otp(string $current): string
    {
        return sprintf('%06d', (int)$current);
    }
}
