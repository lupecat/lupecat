<?php

namespace Lupecat;

use Closure;
use FastRoute\RouteParser\Std as StdParser;
use Lupecat\Error\Exceptions\BindRouterException;
use Lupecat\Error\Exceptions\Routing\PathForRouteException;
use Lupecat\Error\Exceptions\Routing\RoutePatternException;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Route;
use Slim\RouteGroup;
use Slim\Router;

/**
 * Extends from Router (Slim) to add extra validations
 */
final class RouteDispatcher extends Router implements RouterInterface {

    /**
     * @var RouteDispatcher
     */
    private static $slimRouter;

    /**
     * @var Lupecat
     */
    private static $lupecat;

    /**
     * @param Lupecat $Lupecat
     */
    public function __construct(Lupecat $Lupecat)
    {

        parent::__construct(new StdParser);
        self::$lupecat = $Lupecat;

    }

    /**
     * Bind router to framework to access to framework between the router
     *
     * @param Lupecat $application
     * @return RouteDispatcher
     * @throws BindRouterException
     */
    public static function bindToApplication(Lupecat $application)
    {

        // Prevent to bind the framework by user
        if (isset(self::$slimRouter)) {
            throw new BindRouterException(
                "Route is already bind to the framework."
            );
        }

        // Add the router instance
        self::$slimRouter = new self($application);

        return self::$slimRouter;
    }

    /**
     * @param string $pattern
     * @param callable $handler
     * @return RouteInterface
     * @throws RoutePatternException
     */
    public static function get($pattern, callable $handler)
    {
        return self::$slimRouter->map(['get'], $pattern, $handler);
    }

    /**
     * @param string $pattern
     * @param callable $handler
     * @return RouteInterface
     * @throws RoutePatternException
     */
    public static function post($pattern, callable $handler)
    {
        return self::$slimRouter->map(['post'], $pattern, $handler);
    }

    /**
     * @param string $pattern
     * @param callable $handler
     * @return RouteInterface
     * @throws RoutePatternException
     */
    public static function put($pattern, callable $handler)
    {
        return self::$slimRouter->map(['put'], $pattern, $handler);
    }

    /**
     * @param string $pattern
     * @param callable $handler
     * @return RouteInterface
     * @throws RoutePatternException
     */
    public static function patch($pattern, callable $handler)
    {
        return self::$slimRouter->map(['patch'], $pattern, $handler);
    }

    /**
     * @param string $pattern
     * @param callable $handler
     * @return RouteInterface
     * @throws RoutePatternException
     */
    public static function delete($pattern, callable $handler)
    {
        return self::$slimRouter->map(['delete'], $pattern, $handler);
    }

    /**
     * @param string $pattern
     * @param callable $handler
     * @return RouteInterface
     * @throws RoutePatternException
     */
    public static function options($pattern, callable $handler) {
        return self::$slimRouter->map(['options'], $pattern, $handler);
    }

    /**
     * Redirect is not handle by mapping router, is handle by framework
     *
     * @param string $from
     * @param string $to
     * @param int $status
     * @return RouteInterface
     */
    public function redirect($from, $to, $status = 302)
    {
        return self::$lupecat->redirect(
            $from, $to, $status
        );
    }

    /**
     * @param $pattern
     * @param Closure $callable
     * @return RouteGroupInterface|RouteGroup
     */
    public static function group($pattern, Closure $callable) {

        $group = self::$slimRouter->pushGroup($pattern, $callable);
        $group->setContainer(self::$lupecat->getContainer());
        $group(self::$lupecat);

        self::$slimRouter->popGroup();

        return $group;
    }

    /**
     * Call static the path for route
     *
     * @param string $routeName
     * @param array $data
     * @param array $queryParams
     * @return string
     * @throws PathForRouteException
     */
    public static function getPathByName($routeName, array $data = [], array $queryParams = [])
    {

        if(!$route = self::$slimRouter->pathFor($routeName, $data, $queryParams)) {
            throw new PathForRouteException(
                sprintf(
                    "Route %s is not found to get the path", $routeName
                )
            );
        }

        return $route;

    }

    /**
     * Retrieve all the routes
     *
     * @return array
     */
    public static function getAllRoutes() {

        $routes = self::$slimRouter->getRoutes();

        return array_map(function (Route $route) {

            $groups = $route->getGroups();

            return (object) array(
                'identifier' => $route->getIdentifier(),
                'name' => $route->getName(),
                'groups' => array_map(
                    function (RouteGroup $group) {
                        return $group->getPattern();
                    }, $groups
                ),
                'pattern' => $route->getPattern(),
                'methods' => $route->getMethods()
            );

        }, $routes);

    }

    /**
	 * Overwrite method map to control already defined routes must not to be stored
     *
	 * @param string[] $methods
	 * @param string $pattern
	 * @param callable $handler
	 * @return RouteInterface
	 * @throws RoutePatternException
	 */
	public function map($methods, $pattern, $handler)
    {
		// Prepend parent group pattern(s)
        $pattern = $this->prependParentGroupPattern($pattern);

        // According to RFC methods are defined in uppercase (See RFC 7231)
        $methods = $this->uppercaseMethods($methods);

        // Create the RouteInterface
        $route = $this->createRoute($methods, $pattern, $handler);

		// Prevent to store already stored route
        $this->addRoute($route);

        return $route;
	}

    /**
     * @param string $pattern
     * @return string
     */
    public function prependParentGroupPattern($pattern)
    {
        if ($this->routeGroups) {
            $pattern = $this->processGroups() . $pattern;
        }
        return $pattern;
    }

    /**
     * @param array $methods
     * @return array|string[]
     */
    public function uppercaseMethods(array $methods)
    {
        return array_map(function ($method) {
            return strtoupper($method);
        }, $methods);
    }

    /**
     * @param Route $route
     * @throws RoutePatternException
     */
    public function addRoute(Route $route)
    {
        if (isset($this->routes[$route->getIdentifier()])) {
            throw new RoutePatternException(
                sprintf(
                    'Route %s is already defined', $route->getName()
                )
            );
        }

        $this->routes[$route->getIdentifier()] = $route;
        $this->routeCounter++;
    }

}