<?php

declare(strict_types=1);

namespace App\Security\UserInterface\Controller;

use App\Core\Domain\Application\CQRS\EventBus;
use App\Security\Domain\Model\Event\UserRegistered;
use App\Security\Infrastructure\Symfony\Security\SymfonyUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/send-email-verification', name: 'send_email_verification', methods: [Request::METHOD_POST])]
#[IsGranted('ROLE_USER')]
final class SendEmailVerificationController extends AbstractController
{
    public function __invoke(EventBus $eventBus): null
    {
        /** @var SymfonyUser $symfonyUser */
        $symfonyUser = $this->getUser();

        $eventBus->dispatch(new UserRegistered($symfonyUser->getUser()->getId()));

        return null;
    }
}
