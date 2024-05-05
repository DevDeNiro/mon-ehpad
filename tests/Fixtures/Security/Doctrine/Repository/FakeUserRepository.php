<?php

declare(strict_types=1);

namespace Tests\Fixtures\Security\Doctrine\Repository;

use App\Core\Domain\Model\ValueObject\Id;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Enum\Status;
use App\Security\Domain\Model\Exception\UserException;
use App\Security\Domain\Model\ValueObject\Email;
use App\Security\Domain\Model\ValueObject\Password;

final class FakeUserRepository implements UserRepository
{
    /**
     * @var array<string, User>
     */
    public array $users = [];

    public function __construct()
    {
        $this->users['admin+1@email.com'] = new User(
            new Id(),
            Email::fromString('admin+1@email.com'),
            Password::fromString('hashed_password'),
            Status::WaitingForConfirmation
        );
    }

    public function insert(User $user): void
    {
        $this->users[$user->getEmail()->value()] = $user;
    }

    public function isAlreadyUsed(Email|string $email): bool
    {
        return isset($this->users[(string) $email]);
    }

    public function findByEmail(Email $email): User
    {
        if (! isset($this->users[$email->value()])) {
            throw UserException::emailNotFound($email);
        }

        return $this->users[$email->value()];
    }

    public function save(User $user): void
    {
        $this->users[$user->getEmail()->value()] = $user;
    }

    public function findById(Id $id): User
    {
        foreach ($this->users as $user) {
            if ($user->getId()->equals($id)) {
                return $user;
            }
        }

        throw UserException::idNotFound($id);
    }
}
