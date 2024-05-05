<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\CQRS;

use App\Core\Domain\UseCase\Command;
use App\Core\Domain\UseCase\Event;
use App\Core\Domain\UseCase\Handler;
use App\Core\Domain\UseCase\Query;

abstract class AbstractHandler implements Handler
{
    /**
     * @var Command[]|Event[]|Query[]
     */
    private array $messages = [];

    /**
     * @return Command[]|Event[]|Query[]
     */
    public function messages(): array
    {
        return $this->messages;
    }

    protected function add(Command|Query|Event $message): void
    {
        $this->messages[] = $message;
    }
}
