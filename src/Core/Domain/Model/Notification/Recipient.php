<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Notification;

use App\Core\Domain\Model\ValueObject\Email;
use App\Core\Domain\Model\ValueObject\FullName;

final readonly class Recipient
{
    private function __construct(
        private Email $email,
        private ?FullName $fullName = null
    ) {
    }

    public static function create(Email $email, ?FullName $fullName = null): self
    {
        return new self($email, $fullName);
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function fullName(): ?FullName
    {
        return $this->fullName;
    }
}
