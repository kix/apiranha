<?php

namespace tests\Kix\Apiranha\Resource;

use Kix\Apiranha\Exception\InvalidParameterException;
use Kix\Apiranha\ParameterDefinition;
use Kix\Apiranha\Resource\ParameterHydrator;
use Kix\Apiranha\Tests\Mocks\Book;
use Kix\Apiranha\Tests\Mocks\User;

/**
 * Class ParameterHydratorTest
 */
class ParameterHydratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_hydrates_parameters()
    {
        $defns = [
            new ParameterDefinition('userId', 'int', true),
            new ParameterDefinition('book', Book::class, true)
        ];

        $book = new Book();
        $hydrated = ParameterHydrator::hydrateParameters($defns, [1, $book]);
        
        static::assertInternalType('array', $hydrated);
        static::assertEquals(1, $hydrated['userId']);
        static::assertEquals($book, $hydrated['book']);
    }

    /**
     * @test
     * @expectedException \Kix\Apiranha\Exception\InvalidParameterException
     */
    public function it_throws_for_scalar_type_mismatch()
    {
        $defns = [new ParameterDefinition('userId', 'int', true)];

        ParameterHydrator::hydrateParameters($defns, ['lalala']);
    }

    /**
     * @test
     * @expectedException \Kix\Apiranha\Exception\InvalidParameterException
     */
    public function it_throws_for_object_class_mismatch()
    {
        $defns = [new ParameterDefinition('user', User::class, true)];

        ParameterHydrator::hydrateParameters($defns, [new Book()]);
    }
}
