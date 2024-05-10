<?php

declare(strict_types=1);

namespace App\Core\Domain\Application\Notifier;

use App\Core\Domain\Model\ValueObject\Notification\Content;
use App\Core\Domain\Model\ValueObject\Notification\Context;
use App\Core\Domain\Model\ValueObject\Notification\Subject;
use App\Core\Domain\Model\ValueObject\Notification\Template;

interface Notification
{
    public function getSubject(): Subject;

    public function getRecipient(): Recipient;

    public function getContext(): Context;

    public function getContent(): ?Content;

    public function getTemplate(): ?Template;
}
