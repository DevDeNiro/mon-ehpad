<?php

declare(strict_types=1);

namespace Tests\Component\Security\LoginLink;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Identifier;
use App\Security\Domain\Model\Entity\Status;
use App\Security\Domain\Model\Entity\User;
use App\Security\Domain\Model\ValueObject\Password;
use App\Security\Infrastructure\LoginLink\LoginLinkGenerator;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

#[CoversClass(LoginLinkGenerator::class)]
final class LoginLinkGeneratorTest extends KernelTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testShouldGenerateLoginLinkUrl(): void
    {
        /** @var LoginLinkHandlerInterface $loginLinkHandler */
        $loginLinkHandler = self::getContainer()->get(LoginLinkHandlerInterface::class);

        /** @var RequestStack $requestStack */
        $requestStack = self::getContainer()->get(RequestStack::class);
        $requestStack->push(Request::create('http://localhost'));

        $loginLinkGenerator = new LoginLinkGenerator($loginLinkHandler);

        $url = $loginLinkGenerator->generate(
            new User(
                Identifier::generate(),
                Email::create('user@email.com'),
                Password::create('Password123!'),
                Status::Active
            )
        );

        self::assertStringStartsWith('http://localhost/login_check?user=user@email.com', $url->value());
    }
}
