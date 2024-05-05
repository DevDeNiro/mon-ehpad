<?php

declare(strict_types=1);

namespace Tests\Component\Core\Symfony\ValueResolver;

use App\Core\Infrastructure\Symfony\Http\ValueResolver\MessageValueResolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\Fixtures\Core\Domain\UseCase\FakeCommand\Input;
use function Safe\json_encode;

#[CoversClass(MessageValueResolver::class)]
final class MessageValueResolverTest extends KernelTestCase
{
    private MessageValueResolver $messageValueResolver;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var SerializerInterface $serializer */
        $serializer = self::getContainer()->get(SerializerInterface::class);

        /** @var ValidatorInterface $validator */
        $validator = self::getContainer()->get(ValidatorInterface::class);

        $this->messageValueResolver = new MessageValueResolver($serializer, $validator);
    }

    #[Test]
    public function shouldResolveMessage(): void
    {
        $request = Request::create(
            uri: 'http://localhost',
            method: 'POST',
            content: json_encode([
                'foo' => 'bar',
            ])
        );

        $argumentMetadata = new ArgumentMetadata('foo', Input::class, false, false, null);

        $data = $this->messageValueResolver->resolve($request, $argumentMetadata);
        $data = iterator_to_array($data);
        self::assertCount(1, $data);
        $fakeCommand = $data[0];
        self::assertInstanceOf(Input::class, $fakeCommand);
        self::assertSame('bar', $fakeCommand->foo);
    }

    #[Test]
    public function shouldDoNothing(): void
    {
        $request = Request::create(
            uri: 'http://localhost',
            method: 'POST',
            content: json_encode([
                'foo' => 'bar',
            ])
        );
        $argumentMetadata = new ArgumentMetadata('foo', null, false, false, null);
        $data = $this->messageValueResolver->resolve($request, $argumentMetadata);
        $data = iterator_to_array($data);
        self::assertCount(0, $data);
    }

    #[Test]
    public function shouldRaiseBadRequestException(): void
    {
        $request = Request::create(
            uri: 'http://localhost',
            method: 'POST',
            content: 'fail'
        );
        $argumentMetadata = new ArgumentMetadata('foo', Input::class, false, false, null);
        $this->expectException(BadRequestHttpException::class);
        $this->messageValueResolver->resolve($request, $argumentMetadata);
    }

    #[Test]
    public function shouldRaiseValidationFailedException(): void
    {
        $request = Request::create(
            uri: 'http://localhost',
            method: 'POST',
            content: json_encode([
                'foo' => '',
            ])
        );
        $argumentMetadata = new ArgumentMetadata('foo', Input::class, false, false, null);
        $this->expectException(ValidationFailedException::class);
        $this->messageValueResolver->resolve($request, $argumentMetadata);
    }
}
