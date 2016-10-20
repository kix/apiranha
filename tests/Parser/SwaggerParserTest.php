<?php

namespace Tests\Kix\Apiranha\Parser;

use Kix\Apiranha\Parser\SwaggerParser;

/**
 * Class SwaggerParserTest
 */
class SwaggerParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_parses_swagger_definitions()
    {
        $source = file_get_contents(__DIR__.'/../Fixtures/swagger/petstore.json');
        
        $parser = new SwaggerParser();
        $definitions = $parser->parse($source);
        
        static::assertCount(3, $definitions);
    }
}
