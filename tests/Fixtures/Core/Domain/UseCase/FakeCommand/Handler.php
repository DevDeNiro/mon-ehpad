<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeCommand;

use Tests\Fixtures\Core\Domain\CQRS\AbstractHandler;

final class Handler extends AbstractHandler
{
    public function __invoke(Input $fakeCommand): void
    {
        $this->add($fakeCommand);
    }
}
