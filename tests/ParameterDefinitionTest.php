<?php

namespace Tests\Kix\Apiranha;

use Kix\Apiranha\Exception\InvalidArgumentException;
use Kix\Apiranha\ParameterDefinition;

/**
 * @covers \Kix\Apiranha\ParameterDefinition
 */
class ParameterDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_is_initializable()
    {
        $defn = new ParameterDefinition(
            'param',
            'string',
            false
        );

        static::assertInstanceOf(ParameterDefinition::class, $defn);
    }

    /**
     * @test
     * @expectedException \Kix\Apiranha\Exception\InvalidArgumentException
     */
    public function it_throws_for_unsupported_types()
    {
        $defn = new ParameterDefinition(
            'param',
            'jibberish',
            false
        );
    }

    /**
     * @test
     */
    public function it_returns_name()
    {
        $defn = new ParameterDefinition(
            'param',
            'string'
        );

        static::assertEquals('param', $defn->getName());
    }

    /**
     * @test
     */
    public function it_returns_type()
    {
        $defn = new ParameterDefinition(
            'param',
            'string'
        );

        static::assertEquals('string', $defn->getType());
    }

    /**
     * @test
     */
    public function it_returns_is_required()
    {
        $requiredDefn = new ParameterDefinition(
            'param',
            'string',
            true
        );

        static::assertTrue($requiredDefn->isRequired());

        $notRequiredDefn = new ParameterDefinition(
            'param',
            'string',
            false
        );

        static::assertFalse($notRequiredDefn->isRequired());
    }

    /**
     * @test
     */
    public function it_is_not_required_by_default()
    {
        $defn = new ParameterDefinition(
            'param',
            'string'
        );

        static::assertFalse($defn->isRequired());
    }
}
