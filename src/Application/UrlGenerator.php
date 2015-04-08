<?php

namespace Hack\Application;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait UrlGenerator
{
    /**
     * Generates a path from the given parameters.
     *
     * @param  string $route       The name of the route
     * @param  mixed  $parameters  An array of parameters
     * @return string              The generated path
     */
    public function path($route, $parameters = array())
    {
        return $this['url.generator']->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * Generates an absolute URL from the given parameters.
     *
     * @param  string $route        The name of the route
     * @param  mixed  $parameters   An array of parameters
     * @return string               The generated URL
     */
    public function url($route, $parameters = array())
    {
        return $this['url.generator']->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
