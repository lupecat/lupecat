<?php

namespace Lupecat\Patterns\Routing;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ControllerInterface {

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, array $args);

}