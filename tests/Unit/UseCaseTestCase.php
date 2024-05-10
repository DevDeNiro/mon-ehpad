<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Domain\Application\CQRS\Handler\CommandHandler;
use App\Core\Domain\Application\CQRS\Handler\EventHandler;
use App\Core\Domain\Application\CQRS\Handler\QueryHandler;
use App\Core\Domain\Application\CQRS\Message\Command;
use App\Core\Domain\Application\CQRS\Message\Event;
use App\Core\Domain\Application\CQRS\Message\Query;
use App\Core\Domain\Validation\Assert;
use Cake\Chronos\Chronos;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ContainerConstraintValidatorFactory;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\FakerTrait;
use Tests\Fixtures\Core\Infrastructure\Symfony\CQRS\FakeEventBus;
use Tests\Fixtures\Core\Infrastructure\Symfony\DependencyInjection\ValidatorContainer;
use Tests\Fixtures\Core\Infrastructure\Symfony\Notifier\FakeEmailNotifier;
use Tests\ReflectionTrait;

abstract class UseCaseTestCase extends TestCase
{
    use UseCaseAssertionsTrait;
    use EventBusAssertionsTrait;
    use NotifierAssertionsTrait;
    use FakerTrait;
    use ReflectionTrait;

    protected null|QueryHandler|CommandHandler|EventHandler $useCase = null;

    private static ?ValidatorInterface $validator = null;

    private static ?FakeEventBus $fakeEventBus = null;

    private static ?FakeEmailNotifier $fakeNotifier = null;

    public static function eventBus(): FakeEventBus
    {
        if (!self::$fakeEventBus instanceof FakeEventBus) {
            self::$fakeEventBus = new FakeEventBus();
        }

        return self::$fakeEventBus;
    }

    public static function notifier(): FakeEmailNotifier
    {
        if (!self::$fakeNotifier instanceof FakeEmailNotifier) {
            self::$fakeNotifier = new FakeEmailNotifier();
        }

        return self::$fakeNotifier;
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

    protected function setUseCase(QueryHandler|CommandHandler|EventHandler $handler): void
    {
        $this->useCase = $handler;
    }

    protected static function setTestNow(Chronos $now): void
    {
        Chronos::setTestNow($now);
    }

    protected function handle(Command|Query|Event $input): mixed
    {
        if (
            !$this->useCase instanceof QueryHandler
            && !$this->useCase instanceof CommandHandler
            && !$this->useCase instanceof EventHandler
        ) {
            throw new \RuntimeException('Setup use case before execute it.');
        }

        if (!method_exists($this->useCase, '__invoke')) {
            throw new \RuntimeException('Use case must have __invoke method.');
        }

        $reflectionMethod = new \ReflectionMethod($this->useCase, '__invoke');

        if ($reflectionMethod->getReturnType() === null || !$reflectionMethod->getReturnType() instanceof \ReflectionNamedType) {
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
        if (!self::$validator instanceof \Symfony\Component\Validator\Validator\ValidatorInterface) {
            $this->setValidator([]);
        }

        Assert::notNull(self::$validator);

        $constraintViolationList = self::$validator->validate($input);

        if (count($constraintViolationList) !== 0) {
            throw new ValidationFailedException($input, $constraintViolationList);
        }
    }
}
