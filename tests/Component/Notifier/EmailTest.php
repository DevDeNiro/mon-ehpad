<?php

declare(strict_types=1);

namespace Tests\Component\Notifier;

use App\Core\Domain\Model\Notification\Email;
use App\Core\Domain\Model\ValueObject\Email as EmailValueObject;
use App\Core\Domain\Model\ValueObject\Url;
use App\Core\Infrastructure\Notifier\Notifier;
use App\Security\Domain\Model\Notification\EmailVerification;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Mailer\MailerInterface;
use Tests\EntityFactoryTrait;

final class EmailTest extends KernelTestCase
{
    use MailerAssertionsTrait;
    use EntityFactoryTrait;

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
                EmailValueObject::create('admin+1@email.com'),
                Url::create('http://localhost/login_check')
            ),
            'expectedEmail' => 'admin+1@email.com',
        ];
    }
}
