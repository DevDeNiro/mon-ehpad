<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Symfony\Security\Authentication;

use App\Core\Domain\CQRS\CommandBus;
use App\Core\Domain\Model\ValueObject\Email;
use App\Security\Domain\UseCase\ConfirmRegistration\VerifiedUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

final readonly class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    #[\Override]
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): null
    {
        $this->commandBus->execute(
            new VerifiedUser(
                Email::create($token->getUserIdentifier())
            )
        );

        return null;
    }
}
