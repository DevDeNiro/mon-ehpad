<?php

declare(strict_types=1);

namespace Tests\Fixtures\Infrastructure\Symfony\Hasher;

use App\Security\Domain\Entity\User;
use App\Security\Domain\Hasher\PasswordHasherInterface;
use App\Security\Domain\ValueObject\Password;
use App\Security\Domain\ValueObject\PlainPassword;

final readonly class PasswordHash implements PasswordHasherInterface
{
    public function hash(PlainPassword $plainPassword): Password
    {
        return Password::create('hashed_password');
    }

    public function verify(PlainPassword $plainPassword, User $user): bool
    {
        return $plainPassword->value() === $user->password()->value();
    }
}
