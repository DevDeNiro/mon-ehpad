<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeEvent;

use Tests\Fixtures\Core\Domain\CQRS\AbstractHandler;

final class FakeHandler extends AbstractHandler
{
    public function __invoke(FakeEvent $fakeEvent): void
    {
        $this->add($fakeEvent);
    }
}
