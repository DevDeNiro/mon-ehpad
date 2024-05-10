<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Alice\Provider;

use App\Core\Infrastructure\Alice\Provider\Provider;

final class UserProvider implements Provider
{
    public static function password(): string
    {
        return password_hash('password', PASSWORD_BCRYPT);
    }
}
