<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeQuery;

use Tests\Fixtures\Core\Domain\CQRS\AbstractHandler;

final class FakeHandler extends AbstractHandler
{
    public function __invoke(FakeQuery $fakeQuery): string
    {
        $this->add($fakeQuery);

        return $fakeQuery->foo;
    }
}
