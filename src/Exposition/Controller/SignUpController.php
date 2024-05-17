<?php

declare(strict_types=1);

namespace App\Exposition\Controller;

use App\Application\CQRS\CommandBus;
use App\Application\UseCase\SignUp\SignUpInput;
use App\Domain\User\Model\User;
use App\Infrastructure\Security\Symfony\Security\SymfonyUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sign-up', name: 'sign_up', methods: [Request::METHOD_POST])]
final class SignUpController extends AbstractController
{
    public function __invoke(SignUpInput $input, CommandBus $commandBus, Security $security): null
    {
        /** @var User $user */
        $user = $commandBus->execute($input);

        $security->login(new SymfonyUser($user));

        return null;
    }
}
