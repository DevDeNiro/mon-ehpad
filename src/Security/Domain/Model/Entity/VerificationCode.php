<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Entity;

use Cake\Chronos\Chronos;
use Symfony\Component\Uid\Ulid;

class VerificationCode
{
    private Ulid $id;

    private string $code;

    private Chronos $expiresAt;

    public static function create(string $code): self
    {
        $verificationCode = new self();
        $verificationCode->id = new Ulid();
        $verificationCode->code = $code;
        $verificationCode->expiresAt = Chronos::now()->addMinutes(15);

        return $verificationCode;
    }

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt->isPast();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function equals(string $verificationCode): bool
    {
        return $this->code === $verificationCode;
    }
}
