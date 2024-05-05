<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Event;

use App\Core\Domain\UseCase\Event;
use App\Core\Domain\Model\ValueObject\Id;

final readonly class UserRegistered implements Event
{
    public function __construct(private Id $id)
    {
    }

    public function getId(): Id
    {
        return $this->id;
    }
}
