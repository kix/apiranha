<?php

namespace Kix\Apiranha\Tests\Hydrator;

use Kix\Apiranha\Hydrator\ReflectionHydratorListener;
use Kix\Apiranha\ResourceDefinition;
use Kix\Apiranha\Response\ApiResponse;
use Kix\Apiranha\Tests\Mocks\Offer;

/**
 * Class ReflectionHydratorTest
 */
class ReflectionHydratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_hydrates_objects()
    {
        $hydrator = new ReflectionHydratorListener();

        $response = $this
            ->getMockBuilder(ApiResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response
            ->expects(static::once())
            ->method('getData')
            ->willReturn([
                'id' => 1,
                'price' => 2000,
                'created_at' => '2015-01-01'
            ]);

        /** @var Offer $result */
        $result = $hydrator->process($response, new ResourceDefinition('test', 'GET', '/path', Offer::class));

        static::assertInstanceOf(Offer::class, $result);
        static::assertEquals(1, $result->getId());
        static::assertEquals(2000, $result->getPrice());
        static::assertEquals('2015-01-01', $result->getCreatedAt());
    }
}
