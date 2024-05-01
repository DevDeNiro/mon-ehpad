<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\ConfirmRegistration;

use App\Core\Domain\CQRS\Handler;
use App\Security\Domain\Port\Repository\UserRepository;

final readonly class ConfirmRegistration implements Handler
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function __invoke(VerifiedUser $verifiedUser): void
    {
        $user = $this->userRepository->findByEmail($verifiedUser->email());
        $user->confirm();

        $this->userRepository->save($user);
    }
}
