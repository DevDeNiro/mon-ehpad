<?php

declare(strict_types=1);

namespace App\Application\Notifier;

interface Notification
{
    public function getSubject(): string;

    public function getRecipient(): string;

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array;

    public function getContent(): ?string;

    public function getTemplate(): ?string;
}
