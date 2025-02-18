<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Symfony\HttpKernel\ValueResolver;

use App\Core\Domain\Application\CQRS\Message\Command;
use App\Core\Domain\Application\CQRS\Message\Query;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class MessageValueResolver implements ValueResolverInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }

    /**
     * @return iterable<Query|Command>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (
            $argument->getType() === null
            || (
                !is_subclass_of($argument->getType(), Query::class)
                && !is_subclass_of($argument->getType(), Command::class)
            )
        ) {
            return [];
        }

        /** @var class-string<Query|Command> $argumentType */
        $argumentType = $argument->getType();

        try {
            $data = $this->serializer->deserialize(
                $request->getContent(),
                $argumentType,
                'json',
                [
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
                ]
            );

            $constraintViolationList = $this->validator->validate($data);

            if ($constraintViolationList->count() > 0) {
                throw new ValidationFailedException($data, $constraintViolationList);
            }

            return [$data];
        } catch (ExceptionInterface $exception) {
            throw new BadRequestHttpException('Invalid JSON body.', $exception);
        }
    }
}
