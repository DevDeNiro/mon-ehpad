<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine\Entity;

use App\Security\Domain\Model\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[Entity]
#[Table(name: '`user`')]
class DoctrineUser
{
    #[Id]
    #[Column(type: UlidType::NAME)]
    public Ulid $id;

    #[Column(type: Types::STRING)]
    public string $email;

    #[Column(type: Types::STRING)]
    public string $password;

    #[Column(type: Types::STRING)]
    public string $status;

    public static function fromUser(User $user): self
    {
        $entity = new self();
        $entity->id = $user->getId()->value();
        $entity->email = $user->getEmail()->value();
        $entity->password = $user->getPassword()->value();
        $entity->status = $user->getStatus()->value;

        return $entity;
    }
}
