<?php

namespace Tests\Kix\Apiranha\Exception;

use Kix\Apiranha\Exception\UndefinedResourceException;

/**
 * Class UndefinedResourceExceptionTest
 * @covers \Kix\Apiranha\Exception\UndefinedResourceException
 */
class UndefinedResourceExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_is_initializable()
    {
        $exception = new UndefinedResourceException();
        
        static::assertInstanceOf(UndefinedResourceException::class, $exception);
    }

    /**
     * @test
     */
    public function it_suggests_resources()
    {
        $exception = UndefinedResourceException::create('teast', ['test', 'text']);
        
        static::assertContains(
            'Did you mean `test`?',
            $exception->getMessage()
        );
    }

    /**
     * @test
     */
    public function it_does_not_suggest_far_matches()
    {
        $exception = UndefinedResourceException::create('test', ['other', 'something']);

        static::assertNotContains(
            'Did you mean',
            $exception->getMessage()
        );
    }
}
