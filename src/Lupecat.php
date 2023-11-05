<?php

namespace Lupecat;

use Closure;
use Lupecat\Error\DefaultErrorHandler;
use Lupecat\Error\DefaultNotAllowedHandler;
use Lupecat\Error\DefaultNotFoundHandler;
use Lupecat\Error\DefaultPhpErrorHandler;
use Lupecat\Error\Exceptions\BindRouterException;
use Lupecat\Error\Exceptions\ErrorBeforeRunException;
use Lupecat\Error\Exceptions\NotConfiguredException;
use Lupecat\Error\Exceptions\OverridePropertiesException;
use Lupecat\Patterns\Error\ErrorHandlerInterface;
use Lupecat\Patterns\Error\NotAllowedHandlerInterface;
use Lupecat\Patterns\Error\NotFoundHandlerInterface;
use Lupecat\Patterns\Error\PhpErrorHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

final class Lupecat extends App {

    /**
     * Is Lupecat prepared?
     *
     * @var bool
     */
    private $isPrepared = false;

    /**
     * One unique instance
     *
     * @var Lupecat
     */
    private static $framework;

    /**
     * @param array $properties
     * @throws BindRouterException
     * @throws OverridePropertiesException
     */
    public function __construct(array $properties) {

        // If app is already prepared, the properties can't be updated
        if($this->isPrepared) {
            throw new OverridePropertiesException(
                "Properties are already set."
            );
        }

        $container = new ServiceRegistry(
            array_merge(
                $properties, array(
                    // Use our custom Route which extends from a real SlimRouter with enrichment
                    // Bind framework to router
                    'router' => RouteDispatcher::bindToApplication($this)
                )
            )
        );

        // Add the custom framework container
        parent::__construct($container);

        // Disable our custom error handlers
        $this->enableDefaultErrorHandlers();

        // Prevent trailing slash routes (/route/ => /route)
        $this->preventTrailingSlashRoutes();

        // Set framework as prepared
        $this->isPrepared = true;

    }

    /**
     * Prepare the application to be loaded
     *
     * @param array $properties
     * @return Lupecat
     * @throws BindRouterException
     * @throws Exception\RenderWithoutPropertiesException
     * @throws OverridePropertiesException
     */
    public static function boot(array $properties)
    {
        if (!isset(self::$framework)) {
            self::$framework = new self($properties);
        }
        return self::$framework;
    }

    /**
     * @return void
     */
    private function enableDefaultErrorHandlers() {

        $this->getContainer()['errorHandler']      = $this->getErrorHandler();
        $this->getContainer()['phpErrorHandler']   = $this->getPhpErrorHandler();
        $this->getContainer()['notFoundHandler']   = $this->getNotFoundErrorHandler();
        $this->getContainer()['notAllowedHandler'] = $this->getNotAllowedHandler();

    }

    /**
     * @return Closure
     */
    private function getErrorHandler()
    {
        return function (ServiceRegistry $container) {
            return new DefaultErrorHandler($container);
        };
    }

    /**
     * @param ErrorHandlerInterface $closure
     * @return void
     */
    public function setErrorHandler(ErrorHandlerInterface $closure) {
        $this->getContainer()['errorHandler'] = $closure;
    }

    /**
     * @return Closure
     */
    private function getPhpErrorHandler()
    {
        return function (ServiceRegistry $container) {
            return new DefaultPhpErrorHandler($container);
        };
    }

    /**
     * @param PhpErrorHandlerInterface $closure
     * @return void
     */
    public function setPhpErrorHandler(PhpErrorHandlerInterface $closure) {
        $this->getContainer()['phpErrorHandler'] = $closure;
    }

    /**
     * @return Closure
     */
    private function getNotFoundErrorHandler() {
        return function (ServiceRegistry $container) {
            return new DefaultNotFoundHandler($container);
        };
    }

    /**
     * @param NotFoundHandlerInterface $closure
     * @return void
     */
    public function setNotFoundErrorHandler(NotFoundHandlerInterface $closure) {
        $this->getContainer()['notFoundHandler'] = $closure;
    }

    /**
     * @return Closure
     */
    private function getNotAllowedHandler() {
        return function (ServiceRegistry $container) {
            return new DefaultNotAllowedHandler($container);
        };
    }

    /**
     * @param NotAllowedHandlerInterface $closure
     * @return void
     */
    public function setNotAllowedHandler(NotAllowedHandlerInterface $closure) {
        $this->getContainer()['notAllowedHandler'] = $closure;
    }

    /**
     * Lupecat treats a URL pattern with a trailing slash as different to one without.
     * That is, /user and /user/ are different and so can have different callbacks attached
     * This global middleware prevent this behavior.
     *
     * @return void
     */
    private function preventTrailingSlashRoutes() {

        $this->add(function (RequestInterface $request, ResponseInterface $response, callable $next) {
            $uri = $request->getUri();
            $path = $uri->getPath();
            if ($path != '/' && substr($path, -1) == '/') {
                // recursively remove slashes when its more than 1 slash
                while(substr($path, -1) == '/') {
                    $path = substr($path, 0, -1);
                }

                // permanently redirect paths with a trailing slash
                // to their non-trailing counterpart
                $uri = $uri->withPath($path);

                if($request->getMethod() == 'GET') {
                    return $response->withRedirect((string)$uri, 301);
                }

                return $next($request->withUri($uri), $response);

            }

            return $next($request, $response);
        });

    }

    /**
     * @param $silent
     * @return ResponseInterface
     * @throws NotConfiguredException
     * @throws ErrorBeforeRunException
     */
    public function run($silent = false)
    {

        if(!$this->isPrepared) {
            throw new NotConfiguredException(
                "Lupecat isn't configured yet"
            );
        }

        try {

            return parent::run($silent);

        } catch (\Exception $e) {

            throw new ErrorBeforeRunException(
                sprintf(
                    "Lupecat can't run: %s", $e->getMessage()
                )
            );

        }

    }

}