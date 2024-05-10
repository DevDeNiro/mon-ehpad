<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Doctrine\ORM\Repository;

use App\Security\Domain\Application\Repository\VerificationCodeRepository;
use App\Security\Domain\Model\Entity\VerificationCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VerificationCode>
 */
final class DoctrineVerificationCodeRepository extends ServiceEntityRepository implements VerificationCodeRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, VerificationCode::class);
    }

    public function generateCode(): string
    {
        return (string) $this->getEntityManager()
            ->createNativeQuery(
                'SELECT GENERATE_OTP_CODE() as code',
                (new ResultSetMapping())->addScalarResult('code', 'code')
            )
            ->getSingleScalarResult();
    }

    public function insert(VerificationCode $verificationCode): void
    {
        $this->getEntityManager()->persist($verificationCode);
    }

    public function remove(VerificationCode $verificationCode): void
    {
        $this->getEntityManager()->remove($verificationCode);
    }
}
