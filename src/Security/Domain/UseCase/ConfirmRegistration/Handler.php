<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\ConfirmRegistration;

use App\Core\Domain\Application\Repository\PendingOneTimePasswordRepository;
use App\Core\Domain\Model\Exception\OneTimePasswordException;
use App\Core\Domain\Model\ValueObject\OneTimePassword;
use App\Core\Domain\UseCase\Handler as CoreHandler;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Model\Exception\UserException;
use App\Security\Domain\Model\ValueObject\Email;

final readonly class Handler implements CoreHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private PendingOneTimePasswordRepository $pendingOneTimePasswordRepository
    ) {
    }

    public function __invoke(Input $input): void
    {
        $pendingOneTimePassword = $this->pendingOneTimePasswordRepository->findOneByOneTimePassword($input->getOneTimePassword());

        if (null === $pendingOneTimePassword) {
            throw OneTimePasswordException::oneTimePasswordNotFound($input->getOneTimePassword());
        }

        if ($pendingOneTimePassword->isExpired()) {
            throw OneTimePasswordException::pendingOneTimePasswordExpires($pendingOneTimePassword);
        }

        $user = $this->userRepository->findOneByEmail($input->getEmail());

        if (null === $user) {
            throw UserException::emailNotFound($input->getEmail());
        }

        if (!$pendingOneTimePassword->getTarget()->isFor($user, $user->getId())) {
            throw OneTimePasswordException::targetDoesNotMatch($pendingOneTimePassword, $user);
        }

        $user->confirm();

        $this->pendingOneTimePasswordRepository->remove($pendingOneTimePassword);
    }
}
