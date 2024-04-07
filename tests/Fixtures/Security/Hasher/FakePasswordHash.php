<?php

declare(strict_types=1);

namespace Tests\Fixtures\Security\Hasher;

use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Domain\Model\ValueObject\PlainPassword;
use App\Security\Domain\Port\Hasher\PasswordHasherInterface;

final readonly class FakePasswordHash implements PasswordHasherInterface
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
