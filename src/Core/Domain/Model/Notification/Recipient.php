<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Notification;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\FullName;

final readonly class Recipient
{
    private function __construct(
        private Email $emailAddress,
        private ?FullName $fullName = null
    ) {
    }

    public static function create(Email $emailAddress, ?FullName $fullName = null): self
    {
        return new self($emailAddress, $fullName);
    }

    public function emailAddress(): Email
    {
        return $this->emailAddress;
    }

    public function fullName(): ?FullName
    {
        return $this->fullName;
    }
}
