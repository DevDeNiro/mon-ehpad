<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Event;

use App\Core\Domain\Application\CQRS\Message\Event;
use Symfony\Component\Uid\Ulid;

final readonly class UserRegistered implements Event
{
    public function __construct(public Ulid $userId)
    {
    }
}
