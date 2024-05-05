<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\SignUp;

use App\Core\Domain\Application\CQRS\EventBus;
use App\Core\Domain\Model\ValueObject\Id;
use App\Core\Domain\UseCase\Handler as CoreHandler;
use App\Security\Domain\Application\Hasher\PasswordHasher;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Enum\Status;
use App\Security\Domain\Model\Event\UserRegistered;
use App\Security\Domain\Model\ValueObject\Email;
use App\Security\Domain\Model\ValueObject\PlainPassword;

final readonly class Handler implements CoreHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasher $passwordHasher,
        private EventBus $eventBus
    ) {
    }

    public function __invoke(Input $input): void
    {
        $user = new User(
            new Id(),
            $input->email(),
            $this->passwordHasher->hash($input->plainPassword()),
            Status::WaitingForConfirmation
        );

        $this->userRepository->insert($user);

        $this->eventBus->dispatch(new UserRegistered($user->getId()));
    }
}
