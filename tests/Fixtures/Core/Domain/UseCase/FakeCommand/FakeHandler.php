<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeCommand;

use Tests\Fixtures\Core\Domain\CQRS\AbstractHandler;

final class FakeHandler extends AbstractHandler
{
    public function __invoke(FakeCommand $fakeCommand): void
    {
        $this->add($fakeCommand);
    }
}
