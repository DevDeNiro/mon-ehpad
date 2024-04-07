<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject;

use Symfony\Component\Uid\Ulid;

final readonly class Identifier implements Str
{
    public function __construct(private Ulid $value)
    {
    }

    public static function generate(): self
    {
        return new self(new Ulid());
    }

    public static function fromUlid(Ulid $value): self
    {
        return new self($value);
    }

    public function value(): Ulid
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value->toRfc4122();
    }
}
