<?php

declare(strict_types=1);

namespace App\Application\Config\TokenGenerator;

use App\Domain\User\Model\User;
use Cake\Chronos\Chronos;
use Safe\Exceptions\JsonException;
use function Safe\json_encode;
use function strlen;

class TokenGenerator
{
    public function __construct(private string $signingKey)
    {
    }

    /**
     * @throws JsonException
     */
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

        while (($len = strlen($string)) < 20) {
            /** @var int<1, max> $size */
            $size = 20 - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}
