<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Doctrine\DBAL\Types;

use App\Core\Domain\Model\ValueObject\OneTimePassword;

final class OneTimePasswordType extends TextType
{
    public function getName(): string
    {
        return 'one_time_password';
    }

    public function getClass(): string
    {
        return OneTimePassword::class;
    }
}
