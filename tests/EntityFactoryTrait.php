<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Domain\Model\Factory\Factory;
use Tests\Fixtures\Core\Symfony\CQRS\FakeEventBus;

trait EntityFactoryTrait
{
    private static ?FakeEventBus $eventBus = null;

    /**
     * @template T of Factory
     *
     * @param class-string<T> $factory
     *
     * @return T
     */
    protected static function entityFactory(string $factory): Factory
    {
        return new $factory(self::eventBus());
    }

    protected static function eventBus(): FakeEventBus
    {
        if (null === self::$eventBus) {
            self::$eventBus = new FakeEventBus();
        }

        return self::$eventBus;
    }
}
