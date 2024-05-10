<?php

declare(strict_types=1);

namespace App\Security\Domain\Application\Repository;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Id;
use App\Security\Domain\Model\Entity\User;

/**
 * @method User|null findOneById(Id $id)
 * @method User|null findOneByEmail(Email $email)
 */
interface UserRepository
{
    public function isAlreadyUsed(Email|string $email): bool;

    public function insert(User $user): void;
}
