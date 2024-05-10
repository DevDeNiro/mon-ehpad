<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Doctrine\DBAL\Types;

use BackedEnum as T;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * @template T of \BackedEnum
 */
abstract class EnumType extends Type
{
    /**
     * @return class-string<T>
     */
    abstract public function getEnum(): string;

    /**
     * @param $value
     * @param AbstractPlatform $platform
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
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return int|string|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): null|int|string
    {
        if (!$value instanceof \BackedEnum) {
            return null;
        }

        return $value->value;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $enum = $this->getEnum();

        $values = array_map(
            static fn (\BackedEnum $item): string => (string) $item->value,
            $enum::cases()
        );

        return sprintf('VARCHAR(%d)', max(array_map('strlen', $values)));
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
