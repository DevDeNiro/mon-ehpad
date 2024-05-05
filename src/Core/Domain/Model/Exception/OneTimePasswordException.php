<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Exception;

use App\Core\Domain\Model\Entity\PendingOneTimePassword;
use App\Core\Domain\Model\ValueObject\Id;
use App\Core\Domain\Model\ValueObject\OneTimePassword;

final class OneTimePasswordException extends DomainException
{
    public static function idNotFound(Id $id): self
    {
        return new self(
            sprintf(
                'Pending one-time password (id: %s) not found',
                $id
            ),
            ['id' => $id]
        );
    }

    public static function oneTimePasswordNotFound(OneTimePassword $oneTimePassword): self
    {
        return new self(
            sprintf(
                'Pending one-time password (code: %s) not found',
                $oneTimePassword
            ),
            ['one_time_password' => $oneTimePassword]
        );
    }

    public static function noOneTimePasswordAvailable(): self
    {
        return new self('No one-time password available');
    }

    public static function pendingOneTimePasswordExpires(PendingOneTimePassword $pendingOneTimePassword): self
    {
        return new self(
            sprintf(
                'Pending one-time password (id: %s) has expired',
                $pendingOneTimePassword->getId()
            ),
            ['pending_one_time_password' => $pendingOneTimePassword]
        );
    }

    public static function targetDoesNotMatch(PendingOneTimePassword $pendingOneTimePassword, object $target): self
    {
        return new self(
            sprintf(
                'Pending one-time password (code: %s) is not for target %s',
                $pendingOneTimePassword->getOneTimePassword(),
                $target::class
            ),
            [
                'pending_one_time_password' => $pendingOneTimePassword,
                'target' => $target,
            ]
        );
    }
}
