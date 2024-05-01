<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\CQRS;

use App\Core\Domain\CQRS\Command;
use App\Core\Domain\CQRS\Event;
use App\Core\Domain\CQRS\Handler;
use App\Core\Domain\CQRS\Query;

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
