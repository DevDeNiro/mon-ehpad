<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Doctrine\Entity;

use App\Core\Domain\Model\Entity\PendingOneTimePassword;
use App\Core\Domain\Model\ValueObject\Target;
use App\Core\Infrastructure\Doctrine\Type\ChronosType;
use App\Core\Infrastructure\Doctrine\Type\TargetType;
use Cake\Chronos\Chronos;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[Entity]
#[Table(name: 'pending_one_time_password')]
class DoctrinePendingOneTimePassword
{
    #[Id]
    #[Column(type: UlidType::NAME)]
    public Ulid $id;

    #[Column(type: Types::STRING, length: 6, unique: true)]
    public string $oneTimePassword;

    #[Column(type: ChronosType::NAME)]
    public Chronos $expiresAt;

    #[Column(type: TargetType::NAME)]
    public Target $target;

    public static function create(PendingOneTimePassword $pendingOneTimePassword): self
    {
        $self = new self();
        $self->id = $pendingOneTimePassword->getId()->value();
        $self->oneTimePassword = $pendingOneTimePassword->getOneTimePassword()->value();
        $self->expiresAt = $pendingOneTimePassword->getExpiresAt();
        $self->target = $pendingOneTimePassword->getTarget();
        return $self;
    }
}
