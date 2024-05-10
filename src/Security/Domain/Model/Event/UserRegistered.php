<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Event;

use App\Core\Domain\Application\CQRS\Message\Event;
use App\Core\Domain\Model\ValueObject\Id;

final readonly class UserRegistered implements Event
{
    private string $id;

    public function __construct(Id $id)
    {
        $this->id = (string) $id;
    }

    public function getId(): Id
    {
        return Id::fromString($this->id);
    }
}
