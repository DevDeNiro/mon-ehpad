<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Alice\Provider;

use Cake\Chronos\Chronos;

final readonly class ChronosProvider implements Provider
{
    public function chronos(string $time): Chronos
    {
        return new Chronos($time);
    }
}
