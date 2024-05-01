<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Domain\CQRS\Command;
use App\Core\Domain\CQRS\Event;
use App\Core\Domain\CQRS\Handler;
use App\Core\Domain\CQRS\Query;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ContainerConstraintValidatorFactory;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\Fixtures\Core\Infrastructure\Notifier\FakeNotifier;
use Tests\Fixtures\Core\Infrastructure\Symfony\CQRS\FakeEventBus;
use Tests\Fixtures\Core\Infrastructure\Symfony\DependencyInjection\ValidatorContainer;

abstract class UseCaseTestCase extends TestCase
{
    use UseCaseAssertionsTrait;

    protected ?Handler $useCase = null;

    private static ?ValidatorInterface $validator = null;

    private static ?FakeEventBus $fakeEventBus = null;

    private static ?FakeNotifier $fakeNotifier = null;

    public static function eventBus(): FakeEventBus
    {
        if (! self::$fakeEventBus instanceof FakeEventBus) {
            self::$fakeEventBus = new FakeEventBus();
        }

        return self::$fakeEventBus;
    }

    /**
     * @param array<string, ConstraintValidatorInterface> $validators
     */
    protected function setValidator(array $validators): self
    {
        $containerConstraintValidatorFactory = new ContainerConstraintValidatorFactory(new ValidatorContainer($validators));

        self::$validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory($containerConstraintValidatorFactory)
            ->enableAttributeMapping()
            ->getValidator();

        return $this;
    }

    protected static function notifier(): FakeNotifier
    {
        if (! self::$fakeNotifier instanceof FakeNotifier) {
            self::$fakeNotifier = new FakeNotifier();
        }

        return self::$fakeNotifier;
    }

    protected function setUseCase(Handler $handler): void
    {
        $this->useCase = $handler;
    }

    protected static function setTestNow(Chronos $now): void
    {
        Chronos::setTestNow($now);
    }

    protected function handle(Command|Query|Event $input): mixed
    {
        if (! $this->useCase instanceof Handler) {
            throw new \RuntimeException('Setup use case before execute it.');
        }

        if (! method_exists($this->useCase, '__invoke')) {
            throw new \RuntimeException('Use case must have __invoke method.');
        }

        $reflectionMethod = new \ReflectionMethod($this->useCase, '__invoke');

        if ($reflectionMethod->getReturnType() === null || ! $reflectionMethod->getReturnType() instanceof \ReflectionNamedType) {
            throw new \RuntimeException('Use case must have a return type.');
        }

        try {
            $this->validate($input);
        } catch (ValidationFailedException $validationFailedException) {
            self::assertCount(count($this->expectedViolations), $validationFailedException->getViolations());
            foreach ($this->expectedViolations as $key => $expectedViolation) {
                self::assertSame($expectedViolation['propertyPath'], $validationFailedException->getViolations()->get($key)->getPropertyPath());
                self::assertSame($expectedViolation['message'], $validationFailedException->getViolations()->get($key)->getMessage());
            }

            throw $validationFailedException;
        }

        if ($reflectionMethod->getReturnType()->getName() === 'void') {
            $reflectionMethod->invoke($this->useCase, $input);

            return null;
        }

        return $reflectionMethod->invoke($this->useCase, $input);
    }

    private function validate(Query|Command|Event $input): void
    {
        if (! self::$validator instanceof ValidatorInterface) {
            throw new \RuntimeException('Setup validator before use it.');
        }

        $constraintViolationList = self::$validator->validate($input);

        if (count($constraintViolationList) !== 0) {
            throw new ValidationFailedException($input, $constraintViolationList);
        }
    }
}
