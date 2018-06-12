<?php

namespace Kix\Apiranha\Tests\Serializer;

use Kix\Apiranha\Exception\InvalidArgumentException;
use Kix\Apiranha\Exception\RuntimeException;
use Kix\Apiranha\Serializer\SymfonySerializerAdapter;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Class SymfonySerializerAdapterTest
 */
class SymfonySerializerAdapterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     * @group integration
     */
    public function it_deserializes_data()
    {
        $serializer = new Serializer([], [new JsonEncoder()]);
        $adapter = new SymfonySerializerAdapter($serializer);
        $adapter->addContentType('application/json', 'json');
        $data = array(
            'test' => 'me',
            'float' => 1.2,
            'object' => array(
                'with' => array(
                    'complicated' => 'structure',
                    'and' => [1,2,3,4,5]
                ),
            ),
        );

        $result = $adapter->decode('application/json', json_encode($data));

        static::assertEquals($data, $result);
    }

    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage Content type application/json is already registered in the serializer
     */
    public function it_throws_for_already_registered_formats()
    {
        $serializer = $this->createMock(Serializer::class);
        $serializer
            ->expects(static::once())
            ->method('supportsDecoding')
            ->with('json')
            ->willReturn(true);

        $adapter = new SymfonySerializerAdapter($serializer);

        $adapter->addContentType('application/json', 'json');
        $adapter->addContentType('application/json', 'json');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Serializer does not support format doge
     */
    public function it_throws_for_formats_not_supported_by_the_serializer()
    {
        $serializer = $this->createMock(Serializer::class);
        $serializer
            ->expects(static::once())
            ->method('supportsDecoding')
            ->with('doge')
            ->willReturn(false);

        $adapter = new SymfonySerializerAdapter($serializer);

        $adapter->addContentType('application/json', 'doge');
    }
}
