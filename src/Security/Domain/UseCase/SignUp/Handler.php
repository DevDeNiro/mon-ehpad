<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\SignUp;

use App\Core\Domain\Application\CQRS\EventBus;
use App\Core\Domain\Application\CQRS\Handler\CommandHandler;
use App\Security\Domain\Application\Hasher\PasswordHasher;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Event\UserRegistered;

final readonly class Handler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasher $passwordHasher,
        private EventBus $eventBus
    ) {
    }

    public function __invoke(Input $input): User
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
