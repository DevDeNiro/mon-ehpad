<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject;

use App\Core\Domain\Validation\Assert;
use Symfony\Component\Uid\Ulid;

final readonly class Identifier implements Str
{
    public function __construct(
        private Ulid $ulid
    ) {
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->ulid->toBinary();
    }

    public static function generate(): self
    {
        return new self(new Ulid());
    }

    public static function fromUlid(Ulid $ulid): self
    {
        return new self($ulid);
    }

    public static function fromString(string $ulid): self
    {
        Assert::true(Ulid::isValid($ulid));
        return new self(new Ulid($ulid));
    }

    public function value(): Ulid
    {
        return $this->ulid;
    }
}
