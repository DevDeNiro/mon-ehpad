<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Infrastructure\Symfony\DependencyInjection;

use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;

final readonly class ValidatorContainer implements ContainerInterface
{
    /**
     * @param array<string, ConstraintValidatorInterface> $services
     */
    public function __construct(
        private array $services = []
    ) {
    }

    #[\Override]
    public function get($id): object
    {
        if ($this->has($id) === false) {
            throw new \InvalidArgumentException(sprintf('Service "%s" not found.', $id));
        }

        return $this->services[$id];
    }

    #[\Override]
    public function has($id): bool
    {
        return isset($this->services[$id]);
    }
}
