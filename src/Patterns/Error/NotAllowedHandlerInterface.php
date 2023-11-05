<?php

namespace Lupecat\Patterns\Error;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface NotAllowedHandlerInterface {

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $methods
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, array $methods);

}