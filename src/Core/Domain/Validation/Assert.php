<?php

declare(strict_types=1);

namespace App\Core\Domain\Validation;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Webmozart\Assert\InvalidArgumentException;

final class Assert extends \Webmozart\Assert\Assert
{
    public static function phoneNumber(string $value, ?string $message = null): void
    {
        $util = PhoneNumberUtil::getInstance();

        try {
            $phoneNumber = $util->parse($value, 'FR');
        } catch (NumberParseException) {
            throw new InvalidArgumentException(sprintf('Invalid phone number. Got: %s', $value));
        }

        self::true(
            $util->isValidNumber($phoneNumber),
            $message ?? sprintf('Invalid phone number. Got: %s', $value)
        );
    }
}
