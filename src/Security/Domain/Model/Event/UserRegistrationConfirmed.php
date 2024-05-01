<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Event;

use App\Core\Domain\CQRS\Event;
use App\Core\Domain\Model\ValueObject\Identifier;

final readonly class UserRegistrationConfirmed implements Event
{
    private function __construct(
        private Identifier $identifier
    ) {
    }

    public static function create(Identifier $identifier): self
    {
        return new self($identifier);
    }

    public function id(): Identifier
    {
        return $this->identifier;
    }
}
