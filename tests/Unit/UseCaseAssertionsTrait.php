<?php

declare(strict_types=1);

namespace Tests\Unit;

use Symfony\Component\Validator\Exception\ValidationFailedException;

trait UseCaseAssertionsTrait
{
    /**
     * @param array<array{propertyPath: string, message: string}> $expectedViolations
     */
    public function expectedViolations(array $expectedViolations): void
    {
        $this->expectException(ValidationFailedException::class);
    }

    public static function assertEmailSent(): void
    {
        self::assertCount(1, self::notifier()->sentEmails());
    }
}
