<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Alice\Provider;

use Symfony\Component\Uid\Ulid;

final readonly class UidProvider implements Provider
{
    public function ulid(): Ulid
    {
        return new Ulid();
    }
}
