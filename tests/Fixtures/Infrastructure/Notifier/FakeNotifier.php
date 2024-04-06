<?php

declare(strict_types=1);

namespace Tests\Fixtures\Infrastructure\Notifier;

use App\Core\Domain\Notifier\Email\Email;
use App\Core\Domain\Notifier\NotifierInterface;

final class FakeNotifier implements NotifierInterface
{
    /**
     * @var array<Email>
     */
    private array $sentEmails = [];

    public function sendEmail(Email $email): void
    {
        $this->sentEmails[] = $email;
    }

    /**
     * @return array<Email>
     */
    public function sentEmails(): array
    {
        $sentEmails = $this->sentEmails;
        $this->sentEmails = [];

        return $sentEmails;
    }
}
