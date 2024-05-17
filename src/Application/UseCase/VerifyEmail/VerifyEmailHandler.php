<?php

declare(strict_types=1);

namespace App\Application\UseCase\VerifyEmail;

use App\Application\CQRS\Handler\CommandHandler;
use App\Domain\Security\Model\VerificationCode;
use App\Domain\User\Repository\VerificationCodeRepository;

final readonly class VerifyEmailHandler implements CommandHandler
{
    public function __construct(
        private VerificationCodeRepository $verificationCodeRepository
    )
    {
    }

    public function __invoke(VerifyEmailInput $input): void
    {
        $user = $input->user;

        $verificationCode = $user->getVerificationCode();

        $user->verify($input->code);

        /** @var VerificationCode $verificationCode */
        $this->verificationCodeRepository->remove($verificationCode);
    }
}
