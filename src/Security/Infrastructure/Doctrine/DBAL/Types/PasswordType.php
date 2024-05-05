<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine\DBAL\Types;

use App\Core\Infrastructure\Doctrine\DBAL\Types\TextType;
use App\Security\Domain\Model\ValueObject\Password;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class PasswordType extends TextType
{
    public function getName(): string
    {
        return 'password';
    }

    public function getClass(): string
    {
        return Password::class;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'VARCHAR(60)';
    }
}
