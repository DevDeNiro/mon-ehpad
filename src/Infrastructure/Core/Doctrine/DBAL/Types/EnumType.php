<?php

declare(strict_types=1);

namespace App\Infrastructure\Core\Doctrine\DBAL\Types;

use BackedEnum as T;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * @template T of T
 */
abstract class EnumType extends Type
{
    /**
     * @return null|T
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if (!is_string($value) && !is_int($value)) {
            return null;
        }

        return ($this->getEnum())::from($value);
    }

    /**
     * @return class-string<T>
     */
    abstract public function getEnum(): string;

    /**
     * @param mixed $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): null|int|string
    {
        if (!$value instanceof T) {
            return null;
        }

        return $value->value;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $enum = $this->getEnum();

        $values = array_map(
            static fn(T $item): string => (string)$item->value,
            $enum::cases()
        );

        return sprintf('VARCHAR(%d)', max(array_map('strlen', $values)));
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
