<?php

declare(strict_types=1);

namespace Tests\Component\Core\Symfony\CQRS;

use App\Core\Infrastructure\Symfony\CQRS\MessengerQueryBus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Tests\Component\BusTestCase;
use Tests\Fixtures\Core\Domain\UseCase\FakeQuery\Handler;
use Tests\Fixtures\Core\Domain\UseCase\FakeQuery\Input;

#[CoversClass(MessengerQueryBus::class)]
#[TestDox('Messenger bus : ' . MessengerQueryBus::class)]
final class MessengerQueryBusTest extends BusTestCase
{
    protected const string BUS = 'query.bus';

    #[Test]
    public function shouldExecuteCommandSuccessfully(): void
    {
        $handler = $this->getHandler(Handler::class);
        $messengerQueryBus = new MessengerQueryBus($this->bus());
        $foo = $messengerQueryBus->fetch(new Input('bar'));
        self::assertSame('bar', $foo);
        self::assertCount(1, $handler->messages());
    }

    #[Test]
    public function shouldFailedOnValidation(): void
    {
        $handler = $this->getHandler(Handler::class);
        $messengerQueryBus = new MessengerQueryBus($this->bus());
        self::expectException(ValidationFailedException::class);
        $messengerQueryBus->fetch(new Input());
        self::assertCount(0, $handler->messages());
    }
}
