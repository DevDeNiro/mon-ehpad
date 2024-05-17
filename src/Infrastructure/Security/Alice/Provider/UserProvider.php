<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Alice\Provider;

use App\Infrastructure\Core\Alice\Provider\Provider;

final class UserProvider implements Provider
{
    public static function password(): string
    {
        return password_hash('password', PASSWORD_BCRYPT);
    }
}
