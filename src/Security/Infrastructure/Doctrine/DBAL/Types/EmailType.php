<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine\DBAL\Types;

use App\Core\Infrastructure\Doctrine\DBAL\Types\TextType;
use App\Security\Domain\Model\ValueObject\Email;

final class EmailType extends TextType
{
    public function getName(): string
    {
        return 'email';
    }

    public function getClass(): string
    {
        return Email::class;
    }
}
