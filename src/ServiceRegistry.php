<?php

namespace Lupecat;

use Lupecat\Error\Exceptions\PropertyIsNotFoundException;
use Lupecat\Error\Exceptions\PropertyLaunchErrorException;
use Slim\Container;

class ServiceRegistry extends Container
{

    /**
     * Default settings
     *
     * @var array
     */
    private $defaultServiceRegistryProperties = [
        'httpVersion' 						=> '1.1',
        'responseChunkSize' 				=> 4096,
        'outputBuffering' 					=> 'append',
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' 				=> false,
        'addContentLengthHeader' 			=> true,
        'routerCacheFile' 					=> false,
        'enableRenderedResponse'            => false
    ];

    /**
     * Container properties
     *
     * @var array
     */
    private $properties;

    /**
     * Store the framework properties for use by injection
     * Set the default properties but never must be public
     * Don't change on execution time
     *
     * @param array $properties
     */
    public function __construct(array $properties) {

        $this->defaultServiceRegistryProperties =
            array_merge(
                $this->defaultServiceRegistryProperties, isset($properties['settings'] )
                    ? $properties['settings'] : []
            )
        ;

        // Prevent to replace
        unset($properties['settings']);

        // Add the default properties and are replaced by assigment
        $this->properties = array_merge(
            array(
                'settings' => $this->defaultServiceRegistryProperties,
            ), $properties
        );

        parent::__construct($this->properties);

    }

    /**
     * Returns the container properties.
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Add more properties just in time
     *
     * @param array $properties
     */
    public function addProperties(array $properties) {
        $this->properties = array_merge(
            $this->properties, $properties
        );
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     * Extracted and refactor from Lupecat
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed
     *
     * @throws PropertyLaunchErrorException
     * @throws PropertyIsNotFoundException
     */
    public function get($id)
    {

        if (!$this->offsetExists($id)) {
            throw new PropertyIsNotFoundException(
                sprintf('Identifier "%s" is not defined.', $id)
            );
        }

        try {

            return $this->offsetGet($id);

        } catch (\Exception $e) {

            throw new PropertyLaunchErrorException(
                sprintf(
                    'Container launches an exception trying to get property %s: %s', $id, $e->getMessage()
                )
            );

        }
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return $this->offsetExists($id);
    }

    /**
     * @param $name
     * @return mixed
     * @throws PropertyLaunchErrorException
     * @throws PropertyIsNotFoundException
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

}