<?php

declare(strict_types=1);

namespace App\Security\Domain\Application\TokenGenerator;

use App\Security\Domain\Model\Entity\User;
use Cake\Chronos\Chronos;

use function Safe\json_encode;

class TokenGenerator
{
    public function __construct(private string $signingKey)
    {
    }

    public function generateHashedToken(User $user, Chronos $expiresAt, string $token): string
    {
        return base64_encode(
            hash_hmac(
                'sha256',
                json_encode([
                    $token,
                    $user->getId(),
                    $expiresAt->getTimestamp()
                ]),
                $this->signingKey,
                true
            )
        );
    }

    public function generateToken(): string
    {
        $string = '';

        while (($len = \strlen($string)) < 20) {
            /** @var int<1, max> $size */
            $size = 20 - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
