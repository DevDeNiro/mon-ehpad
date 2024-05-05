<?php

declare(strict_types=1);

namespace App\Security\Domain\Application\Repository;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Id;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Exception\UserException;

interface UserRepository
{
    public function insert(User $user): void;

    /**
     * @throws UserException
     */
    public function save(User $user): void;

    public function isAlreadyUsed(Email|string $email): bool;

    /**
     * @throws UserException
     */
    public function findByEmail(Email $email): User;

    /**
     * @throws UserException
     */
    public function findById(Id $id): User;
}
