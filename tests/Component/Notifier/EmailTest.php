<?php

declare(strict_types=1);

namespace Tests\Component\Notifier;

use App\Core\Domain\ValueObject\Email as EmailValueObject;
use App\Core\Domain\Notifier\Email\Email;
use App\Core\Domain\ValueObject\Url;
use App\Core\Infrastructure\Notifier\Notifier;
use App\Security\Domain\Entity\User;
use App\Security\Domain\Notifier\EmailVerification;
use App\Security\Domain\ValueObject\Password;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Mailer\MailerInterface;

final class EmailTest extends KernelTestCase
{
    use MailerAssertionsTrait;

    protected function setUp(): void
    {
        self::bootKernel();
    }

    /**
     * @dataProvider provideEmails
     */
    public function testShouldSendEmail(Email $email, string $expectedEmail): void
    {
        /** @var MailerInterface $mailer */
        $mailer = self::getContainer()->get(MailerInterface::class);
        (new Notifier($mailer))->sendEmail($email);
        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'To', $expectedEmail);
    }

    /**
     * @return iterable<string, array{email: Email, expectedEmail: string}>
     */
    public static function provideEmails(): iterable
    {
        yield 'email verification' => [
            'email' => EmailVerification::create(
                User::register(
                    EmailValueObject::create('admin+1@email.com'),
                    Password::create('password')
                ),
                Url::create('http://localhost/login_check')
            ),
            'expectedEmail' => 'admin+1@email.com',
        ];
    }
}
