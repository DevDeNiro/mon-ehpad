<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\SignUp;

use App\Core\Domain\CQRS\Handler;
use App\Core\Domain\Model\ValueObject\Email;
use App\Security\Domain\Model\Factory\RegisterUserFactory;
use App\Security\Domain\Model\ValueObject\PlainPassword;
use App\Security\Domain\Port\Hasher\PasswordHasherInterface;
use App\Security\Domain\Port\Repository\UserRepository;

final readonly class SignUp implements Handler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private RegisterUserFactory $factory
    ) {
    }

    public function __invoke(NewUser $newUser): void
    {
        $user = $this->factory
            ->withEmail(Email::create($newUser->email))
            ->withPassword($this->passwordHasher->hash(PlainPassword::create($newUser->password)))
            ->build();

        $this->userRepository->register($user);
    }
}
