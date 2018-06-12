<?php

namespace tests\Kix\Apiranha\Exception;

use Kix\Apiranha\Exception\InvalidParameterException;

/**
 * Class InvalidParameterExceptionTest
 */
class InvalidParameterExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_returns_correct_message_for_types()
    {
        $exception = InvalidParameterException::forTypeMismatch('int', '1');

        static::assertEquals('Expected an argument of type int; got string instead', $exception->getMessage());
    }

    /**
     * @test
     */
    public function it_returns_correct_message_for_scalars_instead_of_classes()
    {
        $exception = InvalidParameterException::forTypeMismatch(\stdClass::class, '1');

        static::assertEquals('Expected an instance of class stdClass; got string instead', $exception->getMessage());
    }

    /**
     * @test
     */
    public function it_returns_correct_message_for_multiple_scalars()
    {
        $exception = InvalidParameterException::forTypeMismatch(['int', 'long'], '1');

        static::assertEquals('Expected an argument of types int, long; got string instead', $exception->getMessage());
    }

    /**
     * @test
     */
    public function it_returns_correct_message_for_multiple_classes()
    {
        $exception = InvalidParameterException::forTypeMismatch([\stdClass::class, \Exception::class], '1');

        static::assertEquals('Expected an instance of classes stdClass, Exception; got string instead', $exception->getMessage());
    }
}
