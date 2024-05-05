<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ValueObject;

use App\Core\Domain\Validation\Assert;
use Symfony\Component\Uid\Ulid;

final readonly class Target
{
    /**
     * @param class-string $entity
     */
    private function __construct(private string $entity, private Id $id)
    {
    }

    public static function create(string $entity, Id $id): self
    {
        Assert::classExists($entity);
        return new self($entity, $id);
    }

    /**
     * @param array<string, string> $value
     */
    public static function fromArray(array $value): self
    {
        Assert::keyExists($value, 'entity');
        Assert::classExists($value['entity']);

        Assert::keyExists($value, 'id');
        Assert::true(Ulid::isValid($value['id']));

        return new self($value['entity'], Id::fromString($value['id']));
    }

    public function entity(): string
    {
        return $this->entity;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function isFor(object $entity, Id $id): bool
    {
        if ($this->entity !== $entity::class) {
            return false;
        }

        return $this->id->equals($id);
    }
}
