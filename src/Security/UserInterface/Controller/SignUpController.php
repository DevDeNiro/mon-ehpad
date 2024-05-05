<?php

declare(strict_types=1);

namespace App\Security\UserInterface\Controller;

use App\Core\Domain\Application\CQRS\CommandBus;
use App\Security\Domain\UseCase\SignUp\Input;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sign-up', name: 'sign_up', methods: [Request::METHOD_POST])]
final class SignUpController extends AbstractController
{
    public function __invoke(Input $input, CommandBus $commandBus): RedirectResponse
    {
        $commandBus->execute($input);

        return $this->redirect('/welcome');
    }
}
