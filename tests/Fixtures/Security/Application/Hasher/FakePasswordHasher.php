<?php

declare(strict_types=1);

namespace Tests\Fixtures\Security\Application\Hasher;

use App\Application\Config\Hasher\PasswordHasher;
use App\Domain\User\Model\User;

final readonly class FakePasswordHasher implements PasswordHasher
{
    public function hash(string $plainPassword): string
    {
        return 'hashed_password';
    }

    public function verify(string $plainPassword, User $user): bool
    {
        return $plainPassword === $user->getPassword();
    }
}
