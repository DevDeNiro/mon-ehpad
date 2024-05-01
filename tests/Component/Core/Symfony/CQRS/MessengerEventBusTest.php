<?php

declare(strict_types=1);

namespace Tests\Component\Core\Symfony\CQRS;

use App\Core\Infrastructure\Symfony\CQRS\MessengerEventBus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Tests\Component\BusTestCase;
use Tests\Fixtures\Core\Domain\UseCase\FakeEvent\FakeEvent;
use Tests\Fixtures\Core\Domain\UseCase\FakeEvent\FakeHandler;

#[CoversClass(MessengerEventBus::class)]
#[TestDox('Messenger bus : ' . MessengerEventBus::class)]
final class MessengerEventBusTest extends BusTestCase
{
    protected const string BUS = 'event.bus';

    #[Test]
    public function shouldExecuteCommandSuccessfully(): void
    {
        $handler = $this->getHandler(FakeHandler::class);
        $messengerEventBus = new MessengerEventBus($this->bus());
        $messengerEventBus->dispatch(new FakeEvent('bar'));
        self::assertCount(1, $handler->messages());
    }

    #[Test]
    public function shouldFailedOnValidation(): void
    {
        $handler = $this->getHandler(FakeHandler::class);
        $messengerEventBus = new MessengerEventBus($this->bus());
        self::expectException(ValidationFailedException::class);
        $messengerEventBus->dispatch(new FakeEvent());
        self::assertCount(0, $handler->messages());
    }
}
