<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\VerifyEmail;

use App\Core\Domain\Application\CQRS\Handler\CommandHandler;
use App\Security\Domain\Application\Repository\VerificationCodeRepository;
use App\Security\Domain\Model\Entity\VerificationCode;

final readonly class Handler implements CommandHandler
{
    public function __construct(private VerificationCodeRepository $verificationCodeRepository)
    {
    }

    public function __invoke(Input $input): void
    {
        $user = $input->user;

        $verificationCode = $user->getVerificationCode();

        $user->verify($input->code);

        /** @var VerificationCode $verificationCode */
        $this->verificationCodeRepository->remove($verificationCode);
    }
}
