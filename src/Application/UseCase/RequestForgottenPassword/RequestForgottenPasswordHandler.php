<?php

declare(strict_types=1);

namespace App\Application\UseCase\RequestForgottenPassword;

use App\Application\Config\TokenGenerator\TokenGenerator;
use App\Application\CQRS\Handler\CommandHandler;
use App\Application\Notifier\Notifier;
use App\Domain\Security\Model\ForgottenPasswordRequest;
use App\Domain\Security\Notification\ResetPasswordEmail;
use App\Domain\Security\Repository\ForgottenPasswordRequestRepository;
use App\Domain\User\Exception\ForgottenPasswordAlreadyRequestedException;
use App\Domain\User\Exception\UserNotFoundException;
use App\Domain\User\Repository\UserRepositoryPort;
use Cake\Chronos\Chronos;

final readonly class RequestForgottenPasswordHandler implements CommandHandler
{
    public function __construct(
        private UserRepositoryPort                 $userRepository,
        private ForgottenPasswordRequestRepository $forgottenPasswordRequestRepository,
        private TokenGenerator                     $tokenGenerator,
        private Notifier                           $notifier
    )
    {
    }

    public function __invoke(RequestForgottenPasswordInput $input): void
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
