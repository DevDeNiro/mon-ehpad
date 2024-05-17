<?php

declare(strict_types=1);

namespace App\Exposition\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reset-password/{token}', name: 'reset_password', methods: [Request::METHOD_GET])]
final class ResetPasswordController extends AbstractController
{
    public function __invoke(): null
    {
        return null;
    }
}
