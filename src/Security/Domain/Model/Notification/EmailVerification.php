<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Notification;

use App\Core\Domain\Model\Notification\Email;
use App\Core\Domain\Model\Notification\Recipient;
use App\Core\Domain\Model\ValueObject\Email as EmailValueObject;
use App\Core\Domain\Model\ValueObject\Url;

final readonly class EmailVerification implements Email
{
    private function __construct(
        private EmailValueObject $emailValueObject,
        private Url $url
    ) {
    }

    public static function create(EmailValueObject $emailValueObject, Url $url): self
    {
        return new self($emailValueObject, $url);
    }

    #[\Override]
    public function recipient(): Recipient
    {
        return Recipient::create($this->emailValueObject);
    }

    #[\Override]
    public function context(): array
    {
        return [
            'login_link' => $this->url->value(),
        ];
    }
}
