<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Entity;

use App\Core\Domain\Model\ValueObject\Id;
use App\Core\Domain\Model\ValueObject\OneTimePassword;
use App\Core\Domain\Model\ValueObject\Target;
use Cake\Chronos\Chronos;

final readonly class PendingOneTimePassword
{
    public function __construct(
        private Id $id,
        private OneTimePassword $oneTimePassword,
        private Chronos $expiresAt,
        private Target $target
    ) {
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getOneTimePassword(): OneTimePassword
    {
        return $this->oneTimePassword;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt->isPast();
    }

    public function getTarget(): Target
    {
        return $this->target;
    }

    public function getExpiresAt(): Chronos
    {
        return $this->expiresAt;
    }

    public function isForTarget(object $entity, Id $id): bool
    {
        if ($this->target->entity() !== $entity::class) {
            return false;
        }

        return $this->target->id()->equals($id);
    }
}
