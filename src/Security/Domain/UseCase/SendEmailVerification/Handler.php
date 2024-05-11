<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\SendEmailVerification;

use App\Core\Domain\Application\CQRS\Handler\EventHandler;
use App\Core\Domain\Application\Notifier\Notifier;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Application\Repository\VerificationCodeRepository;
use App\Security\Domain\Model\Entity\VerificationCode;
use App\Security\Domain\Model\Event\UserRegistered;
use App\Security\Domain\Model\Exception\UserNotFoundException;
use App\Security\Domain\Model\Notification\VerificationEmail;

final readonly class Handler implements EventHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private VerificationCodeRepository $verificationCodeRepository,
        private Notifier $notifier
    ) {
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
