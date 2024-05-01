<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\SignUp;

use App\Core\Domain\CQRS\EventBus;
use App\Core\Domain\CQRS\Handler;
use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Identifier;
use App\Security\Domain\Model\Entity\Status;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Event\UserRegistered;
use App\Security\Domain\Model\ValueObject\PlainPassword;
use App\Security\Domain\Port\Hasher\PasswordHasherInterface;
use App\Security\Domain\Port\Repository\UserRepository;

final readonly class SignUp implements Handler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private EventBus $eventBus
    ) {
    }

    public function __invoke(NewUser $newUser): void
    {
        $user = new User(
            Identifier::generate(),
            Email::create($newUser->email),
            $this->passwordHasher->hash(PlainPassword::create($newUser->password)),
            Status::WaitingForConfirmation
        );

        $this->userRepository->insert($user);

        $this->eventBus->dispatch(new UserRegistered($user->id()));
    }
}
