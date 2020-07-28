<?php

namespace LilleBitte\Routing\Annotation;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class Route
{
    /**
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $method;

    public function __construct($path = null, array $data = [])
    {
        $this->setRoute(isset($data['path']) ? $data['path'] : $path);
        $this->setMethod(
            isset($data['method'])
                ? ($data['method'] instanceof Method
                    ? $data['method']->getMethods()
                    : null)
                : ['GET']
        );
    }

    /**
     * Set URI route.
     *
     * @param string $route URI route.
     * @return void
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * Get URI route.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set HTTP method.
     *
     * @param array $method HTTP method.
     * @return void
     */
    public function setMethod(array $method)
    {
        $this->method = $method;
    }

    /**
     * Get HTTP method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
