<?php

namespace Lupecat\Patterns\Error;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ErrorHandlerInterface {

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param \Exception $exception
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, \Exception $exception);

}