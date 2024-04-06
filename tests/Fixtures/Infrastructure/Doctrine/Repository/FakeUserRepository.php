<?php

declare(strict_types=1);

namespace Tests\Fixtures\Infrastructure\Doctrine\Repository;

use App\Core\Domain\ValueObject\Email;
use App\Security\Domain\Entity\User;
use App\Security\Domain\Repository\UserRepository;

final class FakeUserRepository implements UserRepository
{
    /**
     * @var array<int, User>
     */
    public array $users = [];

    /**
     * @var array<string, User>
     */
    public array $emailIndexes = [];

    public function register(User $user): void
    {
        $objectId = spl_object_id($user);
        $this->users[$objectId] = $user;
        $this->emailIndexes[$user->email()->value()] = &$this->users[$objectId];
    }

    public function isAlreadyUsed(Email $email): bool
    {
        return count(array_filter($this->users, static fn (User $user) => $user->email()->equals($email))) > 0;
    }

    public function findByEmail(Email $email): ?User
    {
        return $this->emailIndexes[$email->value()] ?? null;
    }
}
