<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeCommand;

use App\Core\Domain\Application\CQRS\Handler\CommandHandler;
use Tests\Fixtures\Core\Domain\CQRS\AbstractHandler;

final class Handler extends AbstractHandler implements CommandHandler
{
    public function __invoke(Input $fakeCommand): void
    {
        $this->add($fakeCommand);
    }
}
