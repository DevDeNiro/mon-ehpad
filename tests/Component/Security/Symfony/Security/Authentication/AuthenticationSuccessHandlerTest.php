<?php

declare(strict_types=1);

namespace Tests\Component\Security\Symfony\Security\Authentication;

use App\Core\Domain\CQRS\CommandBus;
use App\Core\Domain\Model\ValueObject\Email;
use App\Security\Domain\Port\Repository\UserRepository;
use App\Security\Infrastructure\Symfony\Security\Authentication\AuthenticationSuccessHandler;
use App\Security\Infrastructure\Symfony\Security\SymfonyUser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

#[CoversClass(AuthenticationSuccessHandler::class)]
final class AuthenticationSuccessHandlerTest extends KernelTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        self::bootKernel();
    }

    #[Test]
    public function shouldVerifyUserOnAuthentificationSuccess(): void
    {
        $container = self::getContainer();
        $commandBus = $container->get(CommandBus::class);
        $authenticationSuccessHandler = new AuthenticationSuccessHandler($commandBus);

        $user = $container->get(UserRepository::class)->findByEmail(Email::create('admin+1@email.com'));

        $authenticationSuccessHandler->onAuthenticationSuccess(
            Request::create('/'),
            new UsernamePasswordToken(new SymfonyUser($user), 'main', ['ROLE_USER'])
        );

        $user = $container->get(UserRepository::class)->findByEmail(Email::create('admin+1@email.com'));

        self::assertTrue($user->isActive());
    }
}
