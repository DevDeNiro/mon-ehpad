<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\SignUp;

use App\Core\Domain\CQRS\Handler;
use App\Core\Domain\Notifier\NotifierInterface;
use App\Core\Domain\ValueObject\Email;
use App\Security\Domain\Entity\User;
use App\Security\Domain\Hasher\PasswordHasherInterface;
use App\Security\Domain\LoginLink\LoginLinkGeneratorInterface;
use App\Security\Domain\Notifier\EmailVerification;
use App\Security\Domain\Repository\UserRepository;
use App\Security\Domain\ValueObject\PlainPassword;

final readonly class SignUp implements Handler
{
    public function __construct(
        private UserRepository $userRepository,
        private PasswordHasherInterface $passwordHasher,
        private NotifierInterface $notifier,
        private LoginLinkGeneratorInterface $loginLinkGenerator
    ) {
    }

    public function __invoke(NewUser $newUser): void
    {
        $user = User::register(
            Email::create($newUser->email),
            $this->passwordHasher->hash(PlainPassword::create($newUser->password))
        );
        $this->userRepository->register($user);

        $this->notifier->sendEmail(EmailVerification::create($user, $this->loginLinkGenerator->generate($user)));
    }
}
