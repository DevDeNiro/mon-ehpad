<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Domain\CQRS;

use App\Core\Domain\Application\CQRS\Message\Command;
use App\Core\Domain\Application\CQRS\Message\Event;
use App\Core\Domain\Application\CQRS\Message\Query;

abstract class AbstractHandler
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
