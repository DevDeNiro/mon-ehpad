<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Doctrine\DBAL\Types;

use App\Core\Domain\Model\ValueObject\Id;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class IdType extends AbstractUidType
{
    public const string NAME = 'id';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function getUidClass(): string
    {
        return Id::class;
    }
}
