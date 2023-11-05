<?php

namespace Lupecat\Error;

use Lupecat\Patterns\Error\ErrorHandlerInterface;
use Lupecat\ServiceRegistry;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DefaultErrorHandler implements ErrorHandlerInterface {

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
     * @param \Exception $exception
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, \Exception $exception) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write(
                $exception->getMessage()
            );
    }

}