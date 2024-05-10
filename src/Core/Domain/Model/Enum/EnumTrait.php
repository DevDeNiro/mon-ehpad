<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Enum;

trait EnumTrait
{
    public function equals(self $enum): bool
    {
        return $this->name === $enum->name;
    }
}
