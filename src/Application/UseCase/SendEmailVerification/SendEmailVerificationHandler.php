<?php

declare(strict_types=1);

namespace App\Application\UseCase\SendEmailVerification;

use App\Application\CQRS\Handler\EventHandler;
use App\Application\Notifier\Notifier;
use App\Domain\Security\Model\VerificationCode;
use App\Domain\Security\Notification\VerificationEmail;
use App\Domain\User\Event\UserRegistered;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Repository\UserRepositoryPort;
use App\Domain\User\Repository\VerificationCodeRepository;

final readonly class SendEmailVerificationHandler implements EventHandler
{
    public function __construct(
        private UserRepositoryPort         $userRepository,
        private VerificationCodeRepository $verificationCodeRepository,
        private Notifier                   $notifier
    )
    {
    }

    public function __invoke(UserRegistered $userRegistered): void
    {
        $user = $this->userRepository->findOneById($userRegistered->userId);

        if ($user === null) {
            throw UserNotFoundException::idNotFound($userRegistered->userId);
        }

        $verificationCode = VerificationCode::create($this->verificationCodeRepository->generateCode());

        $this->verificationCodeRepository->insert($verificationCode);

        $user->sendVerificationCode($verificationCode);

        $this->notifier->send(new VerificationEmail($user));
    }
}
