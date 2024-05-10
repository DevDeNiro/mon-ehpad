<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\ConfirmRegistration;

use App\Core\Domain\Application\CQRS\Handler\CommandHandler;
use App\Core\Domain\Application\Repository\PendingOneTimePasswordRepository;
use App\Core\Domain\Model\Exception\OneTimePasswordException;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Application\Security\LoginProgrammatically;
use App\Security\Domain\Model\Exception\UserException;

final readonly class Handler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PendingOneTimePasswordRepository $pendingOneTimePasswordRepository,
        private LoginProgrammatically $loginProgrammatically
    ) {
    }

    public function __invoke(Input $input): void
    {
        $pendingOneTimePassword = $this->pendingOneTimePasswordRepository->findOneByOneTimePassword($input->oneTimePassword());

        if (null === $pendingOneTimePassword) {
            throw OneTimePasswordException::oneTimePasswordNotFound($input->oneTimePassword());
        }

        if ($pendingOneTimePassword->isExpired()) {
            throw OneTimePasswordException::pendingOneTimePasswordExpires($pendingOneTimePassword);
        }

        $user = $this->userRepository->findOneByEmail($input->email());

        if (null === $user) {
            throw UserException::emailNotFound($input->email());
        }

        if (!$pendingOneTimePassword->getTarget()->isFor($user, $user->getId())) {
            throw OneTimePasswordException::targetDoesNotMatch($pendingOneTimePassword, $user);
        }

        $user->confirm();

        $this->pendingOneTimePasswordRepository->remove($pendingOneTimePassword);

        $this->loginProgrammatically->login($user);
    }
}
