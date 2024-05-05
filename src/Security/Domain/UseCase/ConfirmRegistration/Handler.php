<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\ConfirmRegistration;

use App\Core\Domain\Application\Repository\PendingOneTimePasswordRepository;
use App\Core\Domain\Model\Exception\OneTimePasswordException;
use App\Core\Domain\Model\ValueObject\OneTimePassword;
use App\Core\Domain\UseCase\Handler as CoreHandler;
use App\Security\Domain\Application\Repository\UserRepository;
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
        $pendingOneTimePassword = $this->pendingOneTimePasswordRepository->findByOneTimePassword(OneTimePassword::fromString($input->oneTimePassword));

        if ($pendingOneTimePassword->isExpired()) {
            throw OneTimePasswordException::pendingOneTimePasswordExpires($pendingOneTimePassword);
        }

        $user = $this->userRepository->findByEmail(Email::fromString($input->email));

        if (!$pendingOneTimePassword->isForTarget($user, $user->getId())) {
            throw OneTimePasswordException::targetDoesNotMatch($pendingOneTimePassword, $user);
        }

        $user->confirm();

        $this->userRepository->save($user);
        $this->pendingOneTimePasswordRepository->remove($pendingOneTimePassword);
    }
}
