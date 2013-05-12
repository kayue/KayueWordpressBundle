<?php

namespace Kayue\WordpressBundle\Wordpress;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class SiteManager
{
    protected $request;
    protected $sites;
    protected $logger;
    protected $routeCollection;

    private static $availableKeys = array(
        'type', 'resource', 'prefix', 'pattern', 'options', 'defaults', 'requirements', 'hostname_pattern', 'entity_manager'
    );

    function __construct($sites, Request $request, $logger = null)
    {
        $this->sites   = $sites;
        $this->request = $request;
        $this->logger  = $logger;
    }

    function getCurrentSite()
    {
        return $this->getNameByRequest($this->request);
    }

    function getNameByRequest(Request $request)
    {
        $context  = new RequestContext();
        $context->fromRequest($request);
        $pathInfo = $request->getPathInfo();

        $matcher = new UrlMatcher($this->getRouteCollection(), $context);

        try {
            $matched = $matcher->match($pathInfo);

            return $matched['_route'];
        } catch (ResourceNotFoundException $e) {
            if(null !== $this->logger) {
                $this->logger->crit("Current route (" . $pathInfo . ")
                    in host " . $context->getHost() . "
                    not found in WordPress's sites.");
            }

            throw $e;
        }
    }


    /**
     * Gets the RouteCollection instance associated with all WordPress sites.
     *
     * @return RouteCollection A RouteCollection instance
     */
    protected function getRouteCollection()
    {
        if (null === $this->routeCollection) {
            $routeCollection = new RouteCollection();

            foreach ($this->sites as $name => $config) {
                $config = $this->normalizeRouteConfig($config);
                $this->parseRoute($routeCollection, $name, $config);
            }

            $this->routeCollection = $routeCollection;
        }

        return $this->routeCollection;
    }

    /**
     * Normalize route configuration.
     *
     * @param array $config A resource config
     *
     * @return array
     *
     * @throws \InvalidArgumentException if one of the provided config keys is not supported
     */
    private function normalizeRouteConfig(array $config)
    {
        foreach ($config as $key => $value) {
            if (!in_array($key, self::$availableKeys)) {
                throw new \InvalidArgumentException(sprintf(
                    'WordPress routing loader does not support given key: "%s". Expected one of the (%s).',
                    $key, implode(', ', self::$availableKeys)
                ));
            }
        }

        return $config;
    }

    /**
     * Parses a route and adds it to the RouteCollection.
     *
     * @param RouteCollection $collection A RouteCollection instance
     * @param string          $name       Route name
     * @param array           $config     Route definition
     */
    protected function parseRoute(RouteCollection $collection, $name, $config)
    {
        $defaults = isset($config['defaults']) ? $config['defaults'] : array();
        $requirements = isset($config['requirements']) ? $config['requirements'] : array();
        $options = isset($config['options']) ? $config['options'] : array();
        $hostnamePattern = isset($config['hostname_pattern']) ? $config['hostname_pattern'] : null;
        $pattern = isset($config['pattern']) ? $config['pattern'] : "/{path}";

        if (!isset($config['pattern'])) {
            $requirements = array_merge($requirements, array("path" => '.*'));
        }

        $route = new Route($pattern, $defaults, $requirements, $options, $hostnamePattern);

        $collection->add($name, $route);
    }
}