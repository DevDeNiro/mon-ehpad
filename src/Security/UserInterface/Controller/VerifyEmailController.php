<?php

declare(strict_types=1);

namespace App\Security\UserInterface\Controller;

use App\Core\Domain\Application\CQRS\CommandBus;
use App\Security\Domain\UseCase\VerifyEmail\Input;
use App\Security\Infrastructure\Symfony\Security\SymfonyUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/verify-email', name: 'verify_email', methods: [Request::METHOD_POST])]
#[IsGranted('ROLE_USER')]
final class VerifyEmailController extends AbstractController
{
    public function __invoke(Input $input, CommandBus $commandBus): null
    {
        /** @var SymfonyUser $symfonyUser */
        $symfonyUser = $this->getUser();

        $input->user = $symfonyUser->getUser();

        $commandBus->execute($input);

        return null;
    }
}
