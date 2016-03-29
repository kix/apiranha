<?php

namespace Kix\Apiranha\Listener;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;

/**
 * Class LoggerListener
 */
class LoggerListener
{
    private $logger;

    /**
     * LoggerListener constructor.
     *
     * @param $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(Request $request, $response)
    {
        $this->logger->info('Request completed', [
            'req' => $request,
            'res' => $response
        ]);
    }
}
