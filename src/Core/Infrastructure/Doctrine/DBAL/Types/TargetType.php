<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Doctrine\DBAL\Types;

use App\Core\Domain\Model\ValueObject\Target;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;

final class TargetType extends JsonType
{
    public const string NAME = 'target';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Target
    {
        /** @var array{entity: class-string, id: string} $value */
        $value = parent::convertToPHPValue($value, $platform);

        return Target::fromArray($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return $value;
        }

        if (!$value instanceof Target) {
            throw ConversionException::conversionFailedInvalidType($value, self::NAME, [Target::class]);
        }

        return parent::convertToDatabaseValue(
            [
                'entity' => $value->entity(),
                'id' => (string) $value->id(),
            ],
            $platform
        );
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
