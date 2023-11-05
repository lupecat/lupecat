<?php

namespace Lupecat\Routing;

use Lupecat\Patterns\Routing\MiddlewareInterface;
use Lupecat\ServiceRegistry;

/**
 * Implement the middleware interface to make __invokable method
 */
abstract class Middleware implements MiddlewareInterface {

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

}