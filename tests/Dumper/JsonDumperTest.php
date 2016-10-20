<?php

namespace tests\Kix\Apiranha\Dumper;

use Kix\Apiranha\Dumper\JsonDumper;
use Kix\Apiranha\ResourceDefinition;
use Kix\Apiranha\ResourceDefinitionInterface;
use Kix\Apiranha\ParameterDefinition;
use Kix\Apiranha\Tests\Mocks\Offer;
use Kix\Apiranha\Tests\Mocks\User;

/**
 * Class JsonDumperTest
 */
class JsonDumperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_dumps()
    {
        $jsonDumper = new JsonDumper();

        $result = $jsonDumper->dump([new ResourceDefinition(
            'getUserOffers',
            ResourceDefinitionInterface::METHOD_CGET,
            '/api/users/{user.id}/offers',
            Offer::class,
            [
                new ParameterDefinition(
                    'user',
                    User::class,
                    true
                )
            ]
        )]);

//        echo($result);
    }
}
