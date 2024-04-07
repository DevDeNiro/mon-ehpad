<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\SendEmailVerification;

use App\Core\Domain\CQRS\Handler;
use App\Core\Domain\Port\Notifier\NotifierInterface;
use App\Security\Domain\Model\Event\UserRegistered;
use App\Security\Domain\Model\Notification\EmailVerification;
use App\Security\Domain\Port\LoginLink\LoginLinkGeneratorInterface;
use App\Security\Domain\Port\Repository\UserRepository;

final readonly class SendEmailVerification implements Handler
{
    public function __construct(
        private UserRepository $userRepository,
        private NotifierInterface $notifier,
        private LoginLinkGeneratorInterface $loginLinkGenerator,
    ) {
    }

    public function __invoke(UserRegistered $event): void
    {
        $user = $this->userRepository->findById($event->id());

        if (null === $user) {
            throw new \DomainException(sprintf('User with id %s not found', $event->id()));
        }
        $this->notifier->sendEmail(EmailVerification::create($user->email(), $this->loginLinkGenerator->generate($user)));
    }
}
