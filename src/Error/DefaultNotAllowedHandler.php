<?php

namespace Lupecat\Error;

use Lupecat\Patterns\Error\NotAllowedHandlerInterface;
use Lupecat\ServiceRegistry;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DefaultNotAllowedHandler implements NotAllowedHandlerInterface {

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
     * @param array $methods
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, array $methods)
    {
        return $response->withStatus(405)
            ->withHeader('Content-type', 'text/html')
            ->write(
                'Method must be one of: ' . implode(', ', $methods)
            );
    }

}