<?php

declare(strict_types=1);

namespace App\Application\Config\Hasher;

use App\Domain\User\Model\User;

interface PasswordHasher
{
    public function hash(string $plainPassword): string;

    public function verify(string $plainPassword, User $user): bool;
}
