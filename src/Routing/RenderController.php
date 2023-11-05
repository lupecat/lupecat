<?php

namespace Lupecat\Routing;

use Lupecat\Patterns\Routing\ControllerInterface;
use Lupecat\Render\Render;
use Lupecat\ServiceRegistry;
use Psr\Http\Message\ResponseInterface;

/**
 * Implement the controller interface to make __invokable method
 */
abstract class RenderController implements ControllerInterface {

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

    protected function render(ResponseInterface $response, $template, array $data) {

        $responseBody = $response->getBody();
        $responseBody->write(
            Render::load()->createRender(
                $template, $data
            )
        );

        return $response
            ->withBody($responseBody)
            ->withHeader(
                'Content-Type', 'text/html; charset=utf-8'
            );
    }

}