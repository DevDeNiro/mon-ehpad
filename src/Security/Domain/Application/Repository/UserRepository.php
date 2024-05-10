<?php

declare(strict_types=1);

namespace App\Security\Domain\Application\Repository;

use App\Security\Domain\Model\Entity\User;
use Symfony\Component\Uid\Ulid;

/**
 * @method User|null findOneById(Ulid $id)
 * @method User|null findOneByEmail(string $email)
 */
interface UserRepository
{
    public function isAlreadyUsed(string $email): bool;

    public function insert(User $user): void;
}
