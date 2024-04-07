<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Domain\CQRS\Command;
use App\Core\Domain\CQRS\Handler;
use App\Core\Domain\CQRS\Query;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ContainerConstraintValidatorFactory;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\Fixtures\Core\Notifier\FakeNotifier;
use Tests\Fixtures\Core\Symfony\DependencyInjection\ValidatorContainer;

abstract class UseCaseTestCase extends TestCase
{
    use UseCaseAssertionsTrait;

    protected ?Handler $useCase = null;

    private static ?ValidatorInterface $validator = null;

    private static ?FakeNotifier $notifier = null;

    /**
     * @param array<string, ConstraintValidatorInterface> $validators
     */
    protected function setValidator(array $validators): self
    {
        $constraintValidatorFactory = new ContainerConstraintValidatorFactory(new ValidatorContainer($validators));

        self::$validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory($constraintValidatorFactory)
            ->enableAttributeMapping()
            ->getValidator();

        return $this;
    }

    private function validate(Query|Command $input): void
    {
        if (null === self::$validator) {
            throw new \RuntimeException('Setup validator before use it.');
        }

        $violations = self::$validator->validate($input);

        if (0 !== count($violations)) {
            throw new ValidationFailedException($input, $violations);
        }
    }

    protected static function notifier(): FakeNotifier
    {
        if (null === self::$notifier) {
            self::$notifier = new FakeNotifier();
        }

        return self::$notifier;
    }

    protected function setUseCase(Handler $useCase): void
    {
        $this->useCase = $useCase;
    }

    protected function handle(Command|Query $input): mixed
    {
        if (null === $this->useCase) {
            throw new \RuntimeException('Setup use case before execute it.');
        }

        if (!method_exists($this->useCase, '__invoke')) {
            throw new \RuntimeException('Use case must have __invoke method.');
        }

        $method = new \ReflectionMethod($this->useCase, '__invoke');

        if (null === $method->getReturnType() || !$method->getReturnType() instanceof \ReflectionNamedType) {
            throw new \RuntimeException('Use case must have a return type.');
        }

        $this->validate($input);

        if ('void' === $method->getReturnType()->getName()) {
            $method->invoke($this->useCase, $input);

            return null;
        }

        return $method->invoke($this->useCase, $input);
    }
}
