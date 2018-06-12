<?php

namespace tests\Kix\Apiranha\Util;

use Kix\Apiranha\Util\PropertyAccess;
use Kix\Apiranha\Tests\Mocks\Book;
use Kix\Apiranha\Tests\Mocks\User;

/**
 * Class PropertyAccessCheckTest
 */
class PropertyAccessCheckTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_returns_true_for_readable_properties()
    {
        static::assertTrue(PropertyAccess::isPathAccessible(User::class, 'id'));
        static::assertTrue(PropertyAccess::isPathAccessible(User::class, 'username'));
    }

    /**
     * @test
     */
    public function it_returns_false_for_non_readable_properties()
    {
        static::assertFalse(PropertyAccess::isPathAccessible(Book::class, 'notReadable'));
    }

    /**
     * @test
     */
    public function it_returns_false_for_undefined_properties()
    {
        static::assertFalse(PropertyAccess::isPathAccessible(Book::class, 'dogName'));
    }
}
