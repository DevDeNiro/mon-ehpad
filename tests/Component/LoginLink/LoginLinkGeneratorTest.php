<?php

declare(strict_types=1);

namespace Tests\Component\LoginLink;

use App\Core\Domain\ValueObject\Email;
use App\Security\Domain\Entity\User;
use App\Security\Domain\ValueObject\Password;
use App\Security\Infrastructure\LoginLink\LoginLinkGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

final class LoginLinkGeneratorTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testShouldGenerateLoginLinkUrl(): void
    {
        /** @var LoginLinkHandlerInterface $loginLinkHandler */
        $loginLinkHandler = static::getContainer()->get(LoginLinkHandlerInterface::class);

        /** @var RequestStack $requestStack */
        $requestStack = static::getContainer()->get(RequestStack::class);
        $requestStack->push(Request::create('http://localhost'));

        $loginLinkGenerator = new LoginLinkGenerator($loginLinkHandler);

        $url = $loginLinkGenerator->generate(
            User::register(
                Email::create('user@email.com'),
                Password::create('')
            )
        );

        self::assertStringStartsWith('http://localhost/login_check?user=user@email.com', $url->value());
    }
}
