<?php

declare(strict_types=1);

namespace App\Security\UserInterface\Controller;

use App\Core\Domain\Application\CQRS\CommandBus;
use App\Security\Domain\UseCase\ConfirmRegistration\Input;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/confirm-registration', name: 'confirm_registration', methods: [Request::METHOD_POST])]
final class ConfirmRegistration extends AbstractController
{
    public function __invoke(Input $input, CommandBus $commandBus): RedirectResponse
    {
        $commandBus->execute($input);

        return $this->redirect('/welcome');
    }
}
