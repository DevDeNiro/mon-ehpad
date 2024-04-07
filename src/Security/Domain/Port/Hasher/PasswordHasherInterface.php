<?php

declare(strict_types=1);

namespace App\Security\Domain\Port\Hasher;

use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Domain\Model\ValueObject\PlainPassword;

interface PasswordHasherInterface
{
    public function hash(PlainPassword $plainPassword): Password;

    public function verify(PlainPassword $plainPassword, User $user): bool;
}
