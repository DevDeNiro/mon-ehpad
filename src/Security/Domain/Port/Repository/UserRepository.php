<?php

declare(strict_types=1);

namespace App\Security\Domain\Port\Repository;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Identifier;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Port\Repository\Exception\UserNotFoundException;

interface UserRepository
{
    public function insert(User $user): void;

    public function save(User $user): void;

    public function isAlreadyUsed(Email|string $email): bool;

    /**
     * @throws UserNotFoundException
     */
    public function findByEmail(Email $email): User;

    /**
     * @throws UserNotFoundException
     */
    public function findById(Identifier $identifier): User;
}
