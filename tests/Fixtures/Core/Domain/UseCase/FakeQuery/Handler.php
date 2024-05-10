<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeQuery;

use App\Core\Domain\Application\CQRS\Handler\QueryHandler;
use Tests\Fixtures\Core\Domain\CQRS\AbstractHandler;

final class Handler extends AbstractHandler implements QueryHandler
{
    public function __invoke(Input $fakeQuery): string
    {
        $this->add($fakeQuery);

        return $fakeQuery->foo;
    }
}
