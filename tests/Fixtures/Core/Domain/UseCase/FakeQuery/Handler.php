<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeQuery;

use Tests\Fixtures\Core\Domain\CQRS\AbstractHandler;

final class Handler extends AbstractHandler
{
    public function __invoke(Input $fakeQuery): string
    {
        $this->add($fakeQuery);

        return $fakeQuery->foo;
    }
}
