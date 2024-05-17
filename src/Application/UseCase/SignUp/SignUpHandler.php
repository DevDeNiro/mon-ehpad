<?php

declare(strict_types=1);

namespace App\Application\UseCase\SignUp;

use App\Application\Config\Hasher\PasswordHasher;
use App\Application\CQRS\EventBus;
use App\Application\CQRS\Handler\CommandHandler;
use App\Domain\User\Event\UserRegistered;
use App\Domain\User\Model\User;
use App\Domain\User\Repository\UserRepositoryPort;

final readonly class SignUpHandler implements CommandHandler
{
    public function __construct(
        private UserRepositoryPort $userRepository,
        private PasswordHasher     $passwordHasher,
        private EventBus           $eventBus
    )
    {
    }

    public function __invoke(SignUpInput $input): User
    {
        $user = User::register(
            $input->email,
            $this->passwordHasher->hash($input->password),
        );

        $this->userRepository->insert($user);

        $this->eventBus->dispatch(new UserRegistered($user->getId()));

        return $user;
    }
}
