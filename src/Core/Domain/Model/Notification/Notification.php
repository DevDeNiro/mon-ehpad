<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Notification;

interface Notification
{
    /**
     * @return array<string, mixed>
     */
    public function context(): array;
}
