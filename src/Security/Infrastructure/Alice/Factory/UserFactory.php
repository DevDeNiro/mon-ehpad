<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Alice\Factory;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Infrastructure\Alice\Factory\IdFactory;
use App\Security\Domain\Application\Hasher\PasswordHasher;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Enum\Status;
use App\Security\Domain\Model\ValueObject\PlainPassword;

final readonly class UserFactory
{
    public function __construct(private IdFactory $idFactory, private PasswordHasher $passwordHasher)
    {
    }

    public function create(string $current, string $status): User
    {
        return new User(
            $this->idFactory->create('user', (int) $current),
            Email::fromString(sprintf('admin+%d@email.com', (int) $current)),
            $this->passwordHasher->hash(PlainPassword::fromString('Password123!')),
            Status::from($status)
        );
    }
}
