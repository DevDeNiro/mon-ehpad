<?php

declare(strict_types=1);

namespace Tests\Fixtures\Security\Doctrine\Repository;

use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepositoryPort;
use Symfony\Component\Uid\Ulid;

final class FakeUserRepositoryPort implements UserRepositoryPort
{
    /**
     * @var array<string, User>
     */
    public array $users = [];

    public function __construct()
    {
        $this->users['admin+1@email.com'] = User::register('admin+1@email.com', 'hashed_password');
    }

    public function insert(User $user): void
    {
        $this->users[$user->getEmail()] = $user;
    }

    public function isAlreadyUsed(string $email): bool
    {
        return isset($this->users[$email]);
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->users[$email] ?? null;
    }

    public function findOneById(Ulid $id): ?User
    {
        foreach ($this->users as $user) {
            if ($user->getId()->equals($id)) {
                return $user;
            }
        }

        return null;
    }
}
