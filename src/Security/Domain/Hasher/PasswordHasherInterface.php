<?php

declare(strict_types=1);

namespace App\Security\Domain\Hasher;

use App\Security\Domain\Entity\User;
use App\Security\Domain\ValueObject\Password;
use App\Security\Domain\ValueObject\PlainPassword;

interface PasswordHasherInterface
{
    public function hash(PlainPassword $plainPassword): Password;

    public function verify(PlainPassword $plainPassword, User $user): bool;
}
