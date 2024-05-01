<?php

declare(strict_types=1);

namespace Tests\Fixtures\Security\Doctrine\Repository;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Identifier;
use App\Security\Domain\Model\Entity\Status;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Domain\Port\Repository\Exception\UserNotFoundException;
use App\Security\Domain\Port\Repository\UserRepository;

final class FakeUserRepository implements UserRepository
{
    /**
     * @var array<string, User>
     */
    public array $users = [];

    public function __construct()
    {
        $this->users['admin+1@email.com'] = new User(
            Identifier::generate(),
            Email::create('admin+1@email.com'),
            Password::create('hashed_password'),
            Status::WaitingForConfirmation
        );
    }

    #[\Override]
    public function insert(User $user): void
    {
        $this->users[$user->email()->value()] = $user;
    }

    #[\Override]
    public function isAlreadyUsed(Email|string $email): bool
    {
        return isset($this->users[(string) $email]);
    }

    #[\Override]
    public function findByEmail(Email $email): User
    {
        if (! isset($this->users[$email->value()])) {
            throw new UserNotFoundException(sprintf('User (email: %s) not found', $email));
        }

        return $this->users[$email->value()];
    }

    #[\Override]
    public function save(User $user): void
    {
        $this->users[$user->email()->value()] = $user;
    }

    #[\Override]
    public function findById(Identifier $identifier): User
    {
        foreach ($this->users as $user) {
            if ($user->id()->value()->equals($identifier->value())) {
                return $user;
            }
        }

        throw new UserNotFoundException(sprintf('User (id: %s) not found', $identifier));
    }
}
