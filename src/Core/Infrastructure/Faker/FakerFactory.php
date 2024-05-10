<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Faker;

use Faker\Factory;
use Faker\Generator;

final readonly class FakerFactory
{
    public static function create(): Generator
    {
        return Factory::create('fr_FR');
    }
}
