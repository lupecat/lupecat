<?php

namespace Lupecat\Error;

use Lupecat\Patterns\Error\NotFoundHandlerInterface;
use Lupecat\ServiceRegistry;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DefaultNotFoundHandler implements NotFoundHandlerInterface {

    /**
     * @var ServiceRegistry
     */
    protected $serviceRegistry;

    /**
     * Controller constructor.
     * @param ServiceRegistry $serviceRegistry
     */
    public function __construct(ServiceRegistry $serviceRegistry) {
        $this->serviceRegistry = $serviceRegistry;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param $exception
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response)
    {
        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('Page not found');
    }

}