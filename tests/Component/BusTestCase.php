<?php

declare(strict_types=1);

namespace Tests\Component;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Tests\Fixtures\Core\Domain\CQRS\AbstractHandler;

abstract class BusTestCase extends KernelTestCase
{
    /**
     * @var string
     */
    protected const string BUS = self::BUS;

    private MessageBusInterface $messageBus;

    #[\Override]
    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $bus = $container->get(static::BUS);

        self::assertInstanceOf(MessageBusInterface::class, $bus);

        $this->messageBus = $bus;
    }

    protected function bus(): MessageBusInterface
    {
        return $this->messageBus;
    }

    /**
     * @param class-string<AbstractHandler> $handler
     */
    protected function getHandler(string $handler): AbstractHandler
    {
        $handler = self::getContainer()->get($handler);
        self::assertInstanceOf(AbstractHandler::class, $handler);
        return $handler;
    }
}
