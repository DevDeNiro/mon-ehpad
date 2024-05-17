<?php

declare(strict_types=1);

namespace App\Exposition\Controller;

use App\Application\CQRS\CommandBus;
use App\Application\UseCase\VerifyEmail\VerifyEmailInput;
use App\Infrastructure\Security\Symfony\Security\SymfonyUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/verify-email', name: 'verify_email', methods: [Request::METHOD_POST])]
#[IsGranted('ROLE_USER')]
final class VerifyEmailController extends AbstractController
{
    public function __invoke(VerifyEmailInput $input, CommandBus $commandBus): null
    {
        /** @var SymfonyUser $symfonyUser */
        $symfonyUser = $this->getUser();

        $input->user = $symfonyUser->getUser();

        $commandBus->execute($input);

        return null;
    }
}
