<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject\Notification;

use App\Core\Domain\Application\Notifier\Recipient;
use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\Name;

final readonly class EmailRecipient implements Recipient
{
    public function __construct(private Email $email, private ?Name $name = null)
    {
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getName(): ?Name
    {
        return $this->name;
    }
}
