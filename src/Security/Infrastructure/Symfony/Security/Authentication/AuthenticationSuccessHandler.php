<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Symfony\Security\Authentication;

use App\Core\Domain\CQRS\CommandBus;
use App\Security\Domain\UseCase\ConfirmRegistration\VerifiedUser;
use App\Security\Infrastructure\Symfony\Security\User as SymfonyUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

final readonly class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private CommandBus $commandBus)
    {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): null
    {
        if (!$token->getUser() instanceof SymfonyUser) {
            throw new UnsupportedUserException();
        }

        $this->commandBus->execute(new VerifiedUser($token->getUser()->user()));

        return null;
    }
}
