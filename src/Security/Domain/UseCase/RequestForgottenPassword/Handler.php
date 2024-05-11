<?php

declare(strict_types=1);

namespace App\Security\Domain\UseCase\RequestForgottenPassword;

use App\Core\Domain\Application\CQRS\Handler\CommandHandler;
use App\Core\Domain\Application\Notifier\Notifier;
use App\Security\Domain\Application\Repository\ForgottenPasswordRequestRepository;
use App\Security\Domain\Application\Repository\UserRepository;
use App\Security\Domain\Application\TokenGenerator\TokenGenerator;
use App\Security\Domain\Model\Entity\ForgottenPasswordRequest;
use App\Security\Domain\Model\Exception\ForgottenPasswordAlreadyRequestedException;
use App\Security\Domain\Model\Exception\UserNotFoundException;
use App\Security\Domain\Model\Notification\ResetPasswordEmail;
use Cake\Chronos\Chronos;

final readonly class Handler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private ForgottenPasswordRequestRepository $forgottenPasswordRequestRepository,
        private TokenGenerator $tokenGenerator,
        private Notifier $notifier
    ) {
    }

    public function __invoke(Input $input): void
    {
        $user = $this->userRepository->findOneByEmail($input->email);

        if ($user === null) {
            throw UserNotFoundException::emailNotFound($input->email);
        }

        $forgottenPasswordToken = $this->forgottenPasswordRequestRepository->findOneByUser($user);

        if ($forgottenPasswordToken !== null) {
            if (!$forgottenPasswordToken->isExpired()) {
                throw new ForgottenPasswordAlreadyRequestedException($forgottenPasswordToken);
            }

            $this->forgottenPasswordRequestRepository->remove($forgottenPasswordToken);
        }

        $expiresAt = Chronos::now()->addHours(1);

        $token = $this->tokenGenerator->generateToken();
        $hashedToken = $this->tokenGenerator->generateHashedToken($user, $expiresAt, $token);

        $forgottenPasswordRequest = ForgottenPasswordRequest::request($user, $hashedToken, $expiresAt);

        $this->forgottenPasswordRequestRepository->insert($forgottenPasswordRequest);

        $this->notifier->send(new ResetPasswordEmail($forgottenPasswordRequest, $token));
    }
}
