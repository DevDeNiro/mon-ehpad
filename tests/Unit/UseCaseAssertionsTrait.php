<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Domain\CQRS\Event;
use Symfony\Component\Validator\Exception\ValidationFailedException;

trait UseCaseAssertionsTrait
{
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

    public static function assertEmailSent(): void
    {
        self::assertCount(1, static::notifier()->sentEmails());
    }

    public static function assertEventDispatched(Event $event): void
    {
        $serializedEvent = serialize($event);
        $events = static::eventBus()->events();
        self::assertGreaterThanOrEqual(1, count($events));
        self::assertContains($serializedEvent, array_map('serialize', $events));
    }
}
