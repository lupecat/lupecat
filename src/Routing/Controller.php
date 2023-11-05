<?php

namespace Lupecat\Routing;

use Lupecat\Patterns\Routing\ControllerInterface;
use Lupecat\ServiceRegistry;

/**
 * Implement the controller interface to make __invokable method
 */
abstract class Controller implements ControllerInterface {

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