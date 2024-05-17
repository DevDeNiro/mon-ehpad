<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\User\Model\User;
use Symfony\Component\Uid\Ulid;

/**
 * @method User|null findOneById(Ulid $id)
 * @method User|null findOneByEmail(string $email)
 */
interface UserRepositoryPort
{
    public function isAlreadyUsed(string $email): bool;

    public function insert(User $user): void;
}
