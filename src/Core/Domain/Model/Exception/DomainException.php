<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Exception;

abstract class DomainException extends \DomainException
{
    /**
     * @param array<string, mixed> $context
     */
    protected function __construct(string $message = "", private readonly array $context = [])
    {
        parent::__construct($message);
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
