<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Doctrine\Repository;

use App\Core\Domain\ValueObject\Email;
use App\Security\Domain\Entity\User;
use App\Security\Domain\Repository\UserRepository;

final class FakeUserRepository implements UserRepository
{
    /**
     * @var array<string, User>
     */
    public array $users = [];

    public function register(User $user): void
    {
        $this->users[$user->email()->value()] = $user;
    }

    public function isAlreadyUsed(Email $email): bool
    {
        return isset($this->users[$email->value()]);
    }

    public function findByEmail(Email $email): ?User
    {
        return $this->users[$email->value()] ?? null;
    }

    public function confirm(User $user): void
    {
        $this->users[$user->email()->value()] = $user;
    }
}
