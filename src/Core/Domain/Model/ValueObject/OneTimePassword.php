<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject;

use App\Core\Domain\Validation\Assert;

final readonly class OneTimePassword implements Str
{
    private function __construct(private string $value)
    {
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function create(string $value): self
    {
        Assert::regex($value, '/^\d{6}$/', 'Invalid one-time password');
        return new self($value);
    }
}
