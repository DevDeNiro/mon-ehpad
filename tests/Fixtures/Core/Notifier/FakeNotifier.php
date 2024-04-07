<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Notifier;

use App\Core\Domain\Model\Notification\Email;
use App\Core\Domain\Port\Notifier\NotifierInterface;

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
