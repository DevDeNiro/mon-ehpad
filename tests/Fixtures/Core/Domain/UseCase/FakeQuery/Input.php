<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeQuery;

use App\Core\Domain\Application\CQRS\Message\Query;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class Input implements Query
{
    public function __construct(
        #[NotBlank]
        public string $foo = ''
    ) {
    }
}
