<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeEvent;

use App\Core\Domain\Application\CQRS\Handler\EventHandler;
use Tests\Fixtures\Core\Domain\CQRS\AbstractHandler;

final class Handler extends AbstractHandler implements EventHandler
{
    public function __invoke(Input $fakeEvent): void
    {
        $this->add($fakeEvent);
    }
}
