<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Doctrine\Repository;

use App\Core\Domain\Application\Repository\PendingOneTimePasswordRepository;
use App\Core\Domain\Model\Entity\PendingOneTimePassword;
use App\Core\Domain\Model\Exception\OneTimePasswordException;
use App\Core\Domain\Model\ValueObject\Id;
use App\Core\Domain\Model\ValueObject\OneTimePassword;
use App\Core\Infrastructure\Doctrine\Entity\DoctrinePendingOneTimePassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PendingOneTimePassword>
 */
final class DoctrinePendingOneTimePasswordRepository extends ServiceEntityRepository implements PendingOneTimePasswordRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, DoctrinePendingOneTimePassword::class);
    }

    public function generateOneTimePassword(): OneTimePassword
    {
        $resultSetMapping = new ResultSetMapping();

        $resultSetMapping->addScalarResult('code', 'code');

        $query = $this->getEntityManager()->createNativeQuery(
            <<<SQL
                SELECT to_char(codes.new_code, 'FM000000') as code
                FROM (SELECT (trunc((random() * (999999 - 1)) + 1)) as new_code
                      FROM generate_series(1, 99999)) AS codes
                WHERE to_char(codes.new_code, 'FM000000') NOT IN (SELECT one_time_password FROM pending_one_time_password)
                LIMIT 1
            SQL,
            $resultSetMapping
        );

        $code = $query->getSingleScalarResult();

        if (null === $code) {
            throw OneTimePasswordException::noOneTimePasswordAvailable();
        }

        return OneTimePassword::create((string) $code);
    }

    public function findByOneTimePassword(OneTimePassword $oneTimePassword): PendingOneTimePassword
    {
        /** @var DoctrinePendingOneTimePassword|null $doctrinePendingOneTimePassword */
        $doctrinePendingOneTimePassword = $this->findOneBy([
            'oneTimePassword' => (string) $oneTimePassword,
        ]);

        if ($doctrinePendingOneTimePassword === null) {
            throw OneTimePasswordException::pendingOneTimePasswordNotFoundByOneTimePassword($oneTimePassword);
        }

        return $this->hydrateFromDoctrineEntity($doctrinePendingOneTimePassword);
    }

    public function insert(PendingOneTimePassword $pendingOneTimePassword): void
    {
        $this->getEntityManager()->persist(
            DoctrinePendingOneTimePassword::create($pendingOneTimePassword)
        );
        $this->getEntityManager()->flush();
    }

    public function remove(PendingOneTimePassword $pendingOneTimePassword): void
    {
        $doctrinePendingOneTimePassword = $this->find((string) $pendingOneTimePassword->getId());

        if ($doctrinePendingOneTimePassword === null) {
            throw OneTimePasswordException::pendingOneTimePasswordNotFound($pendingOneTimePassword->getId());
        }

        $this->getEntityManager()->remove($doctrinePendingOneTimePassword);
        $this->getEntityManager()->flush();
    }

    private function hydrateFromDoctrineEntity(DoctrinePendingOneTimePassword $pendingOneTimePassword): PendingOneTimePassword
    {
        return new PendingOneTimePassword(
            Id::fromUlid($pendingOneTimePassword->id),
            OneTimePassword::create($pendingOneTimePassword->oneTimePassword),
            $pendingOneTimePassword->expiresAt,
            $pendingOneTimePassword->target
        );
    }
}
