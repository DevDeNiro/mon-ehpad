<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\SignUp;

use App\Core\Domain\CQRS\Handler;
use App\Core\Domain\ValueObject\Identifier;
use App\Security\Domain\Entity\User;
use App\Security\Domain\Hasher\PasswordHasherInterface;
use App\Security\Domain\Repository\UserRepository;
use App\Security\Domain\ValueObject\Email;
use App\Security\Domain\ValueObject\PlainPassword;

final readonly class SignUp implements Handler
{
    public function __construct(private UserRepository $userRepository, private PasswordHasherInterface $passwordHasher)
    {
    }

    public function __invoke(NewUser $newUser): void
    {
        $this->userRepository->register(
            new User(
                Identifier::generate(),
                Email::create($newUser->email),
                PlainPassword::create($newUser->password)->hash($this->passwordHasher)
            )
        );
    }
}
