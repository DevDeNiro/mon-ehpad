<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeCommand;

use App\Core\Domain\CQRS\Command;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class FakeCommand implements Command
{
    public function __construct(
        #[NotBlank]
        public string $foo = ''
    ) {
    }
}
