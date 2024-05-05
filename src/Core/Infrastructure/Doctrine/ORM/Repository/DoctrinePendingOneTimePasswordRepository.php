<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Doctrine\ORM\Repository;

use App\Core\Domain\Application\Repository\PendingOneTimePasswordRepository;
use App\Core\Domain\Model\Entity\PendingOneTimePassword;
use App\Core\Domain\Model\Exception\OneTimePasswordException;
use App\Core\Domain\Model\ValueObject\OneTimePassword;
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
        parent::__construct($managerRegistry, PendingOneTimePassword::class);
    }

    public function generateOneTimePassword(): OneTimePassword
    {

        $resultSetMapping = new ResultSetMapping();

        $resultSetMapping->addScalarResult('code', 'code');

        $query = $this->getEntityManager()->createNativeQuery(
            'SELECT GENERATE_ONE_TIME_PASSWORD() as code',
            $resultSetMapping
        );

        $code = $query->getSingleScalarResult();

        if (null === $code) {
            throw OneTimePasswordException::noOneTimePasswordAvailable();
        }

        return OneTimePassword::fromString((string) $code);
    }

    public function insert(PendingOneTimePassword $pendingOneTimePassword): void
    {
        $this->getEntityManager()->persist($pendingOneTimePassword);
        $this->getEntityManager()->flush();
    }

    public function remove(PendingOneTimePassword $pendingOneTimePassword): void
    {
        $this->getEntityManager()->remove($pendingOneTimePassword);
        $this->getEntityManager()->flush();
    }
}
