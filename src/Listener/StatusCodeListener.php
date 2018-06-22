<?php

namespace Kix\Apiranha\Listener;

use Kix\Apiranha\Response\ApiResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * For cases when the HTTP client itself does not handle exceptions, this listener closes the gap and throws an
 * exception for any non-successful request.
 */
class StatusCodeListener implements AfterResponseListenerInterface
{
    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @throws \Exception
     * @return void
     */
    public function process(RequestInterface $request, ResponseInterface $response): ?ApiResponse
    {
        if ($response->getStatusCode() > 400) {
            throw new \RuntimeException(sprintf(
                'Bad status code: %s',
                $response->getStatusCode()
            ));
        }
    }
}
