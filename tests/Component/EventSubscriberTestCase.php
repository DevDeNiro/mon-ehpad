<?php

declare(strict_types=1);

namespace Tests\Component;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class EventSubscriberTestCase extends KernelTestCase
{
    /**
     * @var class-string<EventSubscriberInterface>
     */
    protected const string EVENT_SUBSCRIBER = self::EVENT_SUBSCRIBER;

    /**
     * @var string[]
     */
    protected static array $dispatchedEvents = [];

    private EventDispatcherInterface $eventDispatcher;

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        self::assertTrue(class_exists(static::EVENT_SUBSCRIBER));
        self::assertContains(EventSubscriberInterface::class, class_implements(static::EVENT_SUBSCRIBER));
    }

    #[\Override]
    public static function tearDownAfterClass(): void
    {
        self::assertCount(
            count(self::getSubscribedEvents()),
            array_unique(self::$dispatchedEvents)
        );
    }

    #[\Override]
    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->eventDispatcher = $container->get('event_dispatcher');
    }

    protected function dispatch(Event $event, string $eventName): void
    {
        self::assertTrue($this->eventDispatcher->hasListeners($eventName));
        self::assertArrayHasKey($eventName, self::getSubscribedEvents());
        $this->eventDispatcher->dispatch($event, $eventName);
        static::$dispatchedEvents[] = $eventName;
    }

    protected static function getKernel(): KernelInterface
    {
        self::assertNotNull(static::$kernel);
        return static::$kernel;
    }

    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    private static function getSubscribedEvents(): array
    {
        /** @var array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>> $subscribedEvents */
        $subscribedEvents = (new \ReflectionMethod(static::EVENT_SUBSCRIBER, 'getSubscribedEvents'))->invoke(null);
        self::assertGreaterThan(0, count($subscribedEvents));

        return $subscribedEvents;
    }
}
