<?php

declare(strict_types=1);

namespace App\Core\Domain\Validation;

use Symfony\Component\Validator\Constraints\PasswordStrength;
use Webmozart\Assert\InvalidArgumentException;

final class Assert extends \Webmozart\Assert\Assert
{
    public static function url(string $value, ?string $message = null): void
    {
        if (false === filter_var($value, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException($message ?? sprintf('Expected a valid URL. Got: %s', $value));
        }
    }

    public static function passwordStrength(string $value, int $strength, ?string $message = null): void
    {
        $length = \strlen($value);

        if (0 === $length) {
            return;
        }
        /** @var array<int> $password */
        $password = count_chars($value, 1);
        $chars = \count($password);

        $control = $digit = $upper = $lower = $symbol = $other = 0;
        foreach (array_keys($password) as $chr) {
            match (true) {
                $chr < 32 || 127 === $chr => $control = 33,
                48 <= $chr && $chr <= 57 => $digit = 10,
                65 <= $chr && $chr <= 90 => $upper = 26,
                97 <= $chr && $chr <= 122 => $lower = 26,
                128 <= $chr => $other = 128,
                default => $symbol = 33,
            };
        }

        $pool = $lower + $upper + $digit + $symbol + $control + $other;
        $entropy = $chars * log($pool, 2) + ($length - $chars) * log($chars, 2);

        $passwordStrength = match (true) {
            $entropy >= 120 => PasswordStrength::STRENGTH_VERY_STRONG,
            $entropy >= 100 => PasswordStrength::STRENGTH_STRONG,
            $entropy >= 80 => PasswordStrength::STRENGTH_MEDIUM,
            $entropy >= 60 => PasswordStrength::STRENGTH_WEAK,
            default => PasswordStrength::STRENGTH_VERY_WEAK,
        };

        if ($passwordStrength < $strength) {
            throw new InvalidArgumentException($message ?? sprintf('The password strength is too low. Please use a stronger password.'));
        }
    }
}
