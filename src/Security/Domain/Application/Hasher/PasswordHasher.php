<?php

declare(strict_types=1);

namespace App\Security\Domain\Application\Hasher;

use App\Security\Domain\Model\Entity\User;

interface PasswordHasher
{
    public function hash(string $plainPassword): string;

    public function verify(string $plainPassword, User $user): bool;
}
