<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Notification;

use App\Core\Domain\Model\Notification\Email;
use App\Core\Domain\Model\Notification\Recipient;
use App\Core\Domain\Model\ValueObject\Email as EmailValueObject;
use App\Core\Domain\Model\ValueObject\Url;

final readonly class EmailVerification implements Email
{
    private function __construct(private EmailValueObject $email, private Url $loginLink)
    {
    }

    public static function create(EmailValueObject $email, Url $loginLink): self
    {
        return new self($email, $loginLink);
    }

    public function recipient(): Recipient
    {
        return Recipient::create($this->email);
    }

    public function context(): array
    {
        return [
            'login_link' => $this->loginLink->value(),
        ];
    }
}
