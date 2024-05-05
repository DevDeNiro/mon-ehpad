<?php

declare(strict_types=1);

namespace Tests\Unit;

use Symfony\Component\Validator\Exception\ValidationFailedException;
use Tests\EventBusAssertionsTrait;

trait UseCaseAssertionsTrait
{
    use EventBusAssertionsTrait;

    /**
     * @var array<array{propertyPath: string, message: string}>
     */
    private array $expectedViolations = [];

    /**
     * @param array<array{propertyPath: string, message: string}> $expectedViolations
     */
    public function expectedViolations(array $expectedViolations): void
    {
        $this->expectException(ValidationFailedException::class);
        $this->expectedViolations = $expectedViolations;
    }
}
