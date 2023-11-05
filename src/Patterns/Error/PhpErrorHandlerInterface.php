<?php

namespace Lupecat\Patterns\Error;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface PhpErrorHandlerInterface {

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, $error);

}