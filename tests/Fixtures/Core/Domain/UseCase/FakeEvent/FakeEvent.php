<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\UseCase\FakeEvent;

use App\Core\Domain\CQRS\Event;
use Symfony\Component\Validator\Constraints\NotBlank;

final readonly class FakeEvent implements Event
{
    public function __construct(
        #[NotBlank]
        public string $foo = ''
    ) {
    }
}
