<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Entity;

use Cake\Chronos\Chronos;
use Symfony\Component\Uid\Ulid;

class ForgottenPasswordRequest
{
    private Ulid $id;

    private User $user;

    private Chronos $expiresAt;

    private string $hashedToken;

    public static function request(User $user, string $hashedToken, Chronos $expiresAt): self
    {
        $request = new self();
        $request->id = new Ulid();
        $request->expiresAt = $expiresAt;
        $request->hashedToken = $hashedToken;
        $request->user = $user;

        return $request;
    }

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt->isPast();
    }
}
