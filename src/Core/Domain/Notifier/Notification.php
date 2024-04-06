<?php

declare(strict_types=1);

namespace App\Core\Domain\Notifier;

interface Notification
{
    /**
     * @return array<string, mixed>
     */
    public function context(): array;
}
