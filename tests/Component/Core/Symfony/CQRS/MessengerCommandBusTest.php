<?php

declare(strict_types=1);

namespace Tests\Component\Core\Symfony\CQRS;

use App\Core\Infrastructure\Symfony\CQRS\MessengerCommandBus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Tests\Component\BusTestCase;
use Tests\Fixtures\Core\Domain\UseCase\FakeCommand\FakeCommand;
use Tests\Fixtures\Core\Domain\UseCase\FakeCommand\FakeHandler;

#[CoversClass(MessengerCommandBus::class)]
#[TestDox('Messenger bus : ' . MessengerCommandBus::class)]
final class MessengerCommandBusTest extends BusTestCase
{
    protected const string BUS = 'command.bus';

    #[Test]
    public function shouldExecuteCommandSuccessfully(): void
    {
        $handler = $this->getHandler(FakeHandler::class);
        $messengerCommandBus = new MessengerCommandBus($this->bus());
        $messengerCommandBus->execute(new FakeCommand('bar'));
        self::assertCount(1, $handler->messages());
    }

    #[Test]
    public function shouldFailedOnValidation(): void
    {
        $handler = $this->getHandler(FakeHandler::class);
        $messengerCommandBus = new MessengerCommandBus($this->bus());
        self::expectException(ValidationFailedException::class);
        $messengerCommandBus->execute(new FakeCommand());
        self::assertCount(0, $handler->messages());
    }
}
