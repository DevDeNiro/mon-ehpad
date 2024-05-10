<?php

declare(strict_types=1);

namespace Tests;

trait ReflectionTrait
{
    protected static function setValue(object $object, string $property, mixed $value): void
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setValue($object, $value);
    }
}
