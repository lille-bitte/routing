<?php

namespace LilleBitte\Routing\Annotation;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class Method
{
    /**
     * @var array
     */
    private $methods;

    public function __construct($methods = [])
    {
        $this->setMethods($methods);
    }

    /**
     * Add HTTP method to method list.
     *
     * @param string $method HTTP method.
     * @return void
     */
    public function addMethod(string $method)
    {
        $this->methods[] = $method;
    }

    /**
     * Set HTTP methods list.
     *
     * @param array $methods HTTP methods list.
     * @return void
     */
    public function setMethods(array $methods)
    {
        $this->methods = $methods;
    }

    /**
     * Get HTTP methods list.
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }
}
