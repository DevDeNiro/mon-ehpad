<?php

declare(strict_types=1);

namespace App\Security\Domain\Notifier;

use App\Core\Domain\Notifier\Email\Email;
use App\Core\Domain\Notifier\Email\Recipient;
use App\Core\Domain\ValueObject\Url;
use App\Security\Domain\Entity\User;

final readonly class EmailVerification implements Email
{
    private function __construct(private User $user, private Url $loginLink)
    {
    }

    public static function create(User $user, Url $loginLink): self
    {
        return new self($user, $loginLink);
    }

    public function recipient(): Recipient
    {
        return Recipient::create($this->user->email());
    }

    public function context(): array
    {
        return [
            'login_link' => $this->loginLink->value(),
        ];
    }
}
