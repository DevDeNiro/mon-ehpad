<?php

declare(strict_types=1);

namespace App\Exposition\Controller;

use App\Application\CQRS\CommandBus;
use App\Application\UseCase\RequestForgottenPassword\RequestForgottenPasswordInput;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/request-forgotten-password', name: 'request_forgotten_password', methods: [Request::METHOD_POST])]
final class RequestForgottenPasswordController extends AbstractController
{
    public function __invoke(RequestForgottenPasswordInput $input, CommandBus $commandBus): null
    {
        $commandBus->execute($input);

        return null;
    }
}
