<?php

namespace tests\Kix\Apiranha\Response;

use Kix\Apiranha\Response\ApiResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ApiReponseTest
 */
class ApiReponseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function it_returns_data()
    {
        $wrapped = $this->createMock(ResponseInterface::class);

        $data = ['hello' => 'world'];
        $response = new ApiResponse($wrapped, $data);

        static::assertEquals($data, $response->getData());
    }

    /**
     * @test
     */
    public function it_proxies_calls_to_wrapped_response()
    {
        $wrapped = $this->createMock(ResponseInterface::class);
        $wrapped->expects(static::once())->method('getBody')->willReturn('body');

        $response = new ApiResponse($wrapped, []);
        static::assertEquals('body', $response->getBody());
    }
}
