<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeEvent;

use App\Core\Domain\UseCase\Event;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class Input implements Event
{
    public function __construct(
        #[NotBlank]
        public string $foo = ''
    ) {
    }
}
