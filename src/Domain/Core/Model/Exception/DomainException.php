<?php

declare(strict_types=1);

namespace App\Domain\core\Model\Exception;

/**
 * @template T of array<string, mixed>
 */
abstract class DomainException extends \DomainException
{
    /**
     * @param T $context
     */
    protected function __construct(
        string                 $message = '',
        private readonly array $context = []
    )
    {
        parent::__construct($message);
    }

    /**
     * @return ($key is null ? T : mixed)
     */
    public function getContext(?string $key = null): mixed
    {
        if ($key !== null) {
            return $this->context[$key];
        }

        return $this->context;
    }
}
