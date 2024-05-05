<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Doctrine\DBAL\Types;

use App\Core\Domain\Model\ValueObject\Text;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;

abstract class TextType extends StringType
{
    /**
     * @return class-string<Text>
     */
    abstract public function getClass(): string;

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Text
    {
        if (!is_string($value)) {
            return null;
        }

        return ($this->getClass())::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (!$value instanceof Text) {
            return null;
        }

        if ($value::class !== $this->getClass()) {
            throw ConversionException::conversionFailedInvalidType($value, self::lookupName($this), [$this->getClass()]);
        }

        return $value->value();
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
