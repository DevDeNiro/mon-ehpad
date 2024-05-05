<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Doctrine\Type;

use Cake\Chronos\Chronos;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

final class ChronosType extends Type
{
    public const string NAME = 'chronos';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'TIMESTAMP(6) WITHOUT TIME ZONE';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Chronos
    {
        if (! is_int($value)) {
            return null;
        }

        return Chronos::createFromTimestamp($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return $value;
        }

        if (! $value instanceof Chronos) {
            throw ConversionException::conversionFailedInvalidType($value, self::NAME, [Chronos::class]);
        }

        return $value->getTimestamp();
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
