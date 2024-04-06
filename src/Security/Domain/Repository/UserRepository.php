<?php

declare(strict_types=1);

namespace App\Security\Domain\Repository;

use App\Core\Domain\ValueObject\Email;
use App\Security\Domain\Entity\User;

interface UserRepository
{
    public function register(User $user): void;

    public function isAlreadyUsed(Email $email): bool;

    public function findByEmail(Email $email): ?User;
}
