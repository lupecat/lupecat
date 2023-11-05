<?php

namespace Lupecat\Error;

use Lupecat\Patterns\Error\PhpErrorHandlerInterface;
use Lupecat\ServiceRegistry;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DefaultPhpErrorHandler implements PhpErrorHandlerInterface {

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
     * @param $error
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, $error)
    {
        return $response->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write(
                sprintf(
                    "Something go wrong %s", $error
                )
            );
    }

}