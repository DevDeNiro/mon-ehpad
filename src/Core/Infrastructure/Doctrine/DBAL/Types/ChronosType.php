<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Doctrine\DBAL\Types;

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
        if (!is_string($value)) {
            return null;
        }

        return Chronos::createFromFormat('Y-m-d H:i:s.u', $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Chronos) {
            throw ConversionException::conversionFailedInvalidType($value, self::NAME, [Chronos::class]);
        }

        return $value->format('Y-m-d H:i:s.u');
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
