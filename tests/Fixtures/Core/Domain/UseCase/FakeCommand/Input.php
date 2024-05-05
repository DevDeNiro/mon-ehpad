<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeCommand;

use App\Core\Domain\UseCase\Command;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class Input implements Command
{
    public function __construct(
        #[NotBlank]
        public string $foo = ''
    ) {
    }
}
