<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Application\CQRS\Message\Event;
use Symfony\Component\Uid\Ulid;

final readonly class UserRegistered implements Event
{
    public function __construct(public Ulid $userId)
    {
    }
}
