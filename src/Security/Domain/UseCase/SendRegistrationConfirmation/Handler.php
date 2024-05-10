<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\SendRegistrationConfirmation;

use App\Core\Domain\Application\CQRS\Handler\EventHandler;
use App\Core\Domain\Application\Notifier\Notifier;
use App\Core\Domain\Application\Repository\PendingOneTimePasswordRepository;
use App\Core\Domain\Model\Entity\PendingOneTimePassword;
use App\Core\Domain\Model\ValueObject\Id;
use App\Core\Domain\Model\ValueObject\Target;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\Event\UserRegistered;
use App\Security\Domain\Model\Exception\UserException;
use App\Security\Domain\Model\Notification\RegistrationConfirmationEmail;
use Cake\Chronos\Chronos;

final readonly class Handler implements EventHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PendingOneTimePasswordRepository $pendingOneTimePasswordRepository,
        private Notifier $notifier
    ) {
    }

    public function __invoke(UserRegistered $userRegistered): void
    {
        $user = $this->userRepository->findOneById($userRegistered->getId());

        if (null === $user) {
            throw UserException::idNotFound($userRegistered->getId());
        }

        $pendingOneTimePassword = new PendingOneTimePassword(
            new Id(),
            $this->pendingOneTimePasswordRepository->generateOneTimePassword(),
            Chronos::now()->addMinutes(15),
            Target::create(User::class, $user->getId())
        );

        $this->pendingOneTimePasswordRepository->insert($pendingOneTimePassword);

        $this->notifier->send(new RegistrationConfirmationEmail($user, $pendingOneTimePassword));
    }
}
