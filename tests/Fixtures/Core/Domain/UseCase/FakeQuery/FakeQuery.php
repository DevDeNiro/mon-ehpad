<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeQuery;

use App\Core\Domain\CQRS\Query;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class FakeQuery implements Query
{
    public function __construct(
        #[NotBlank]
        public string $foo = ''
    ) {
    }
}
