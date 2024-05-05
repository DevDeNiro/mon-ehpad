<?php

declare(strict_types=1);

namespace Tests\Fixtures\Security\Application\Hasher;

use App\Security\Domain\Application\Hasher\PasswordHasher;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Domain\Model\ValueObject\PlainPassword;

final readonly class FakePasswordHasher implements PasswordHasher
{
    public function hash(PlainPassword $plainPassword): Password
    {
        return Password::fromString('hashed_password');
    }

    public function verify(PlainPassword $plainPassword, User $user): bool
    {
        return $plainPassword->value() === $user->getPassword()->value();
    }
}
