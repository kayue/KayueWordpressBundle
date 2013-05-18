<?php

namespace Kayue\WordpressBundle\Wordpress;

use Psr\Log\LoggerInterface;
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

    function __construct($sites, Request $request, LoggerInterface $logger = null)
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
                $this->logger->critical("Current route (" . $pathInfo . ")
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
                $this->parseRoute($routeCollection, $name, $config);
            }

            $this->routeCollection = $routeCollection;
        }

        return $this->routeCollection;
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
        $path = isset($config['path']) ? $config['path'] : '/{path}';
        $defaults = isset($config['defaults']) ? $config['defaults'] : array();
        $requirements = isset($config['requirements']) ? $config['requirements'] : array();
        $options = isset($config['options']) ? $config['options'] : array();
        $host = isset($config['host']) ? $config['host'] : '';

        if (!isset($config['pattern'])) {
            $requirements = array_merge($requirements, array("path" => '.*'));
        }

        $route = new Route($path, $defaults, $requirements, $options, $host);

        $collection->add($name, $route);
    }
}