<?php

namespace Kix\Apiranha\Listener;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Kix\Apiranha\Response\SubclassResponse;

/**
 * Class ResponseWrapperListener
 */
class ResponseWrapperListener
{
    public function __invoke(Request $request, Response $response)
    {
        return new SubclassResponse($response);
    }
}
