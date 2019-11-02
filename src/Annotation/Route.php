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
		$this->addRoute(isset($data['path']) ? $data['path'] : $path);
		$this->addMethod(
			isset($data['method'])
				? ($data['method'] instanceof Method
					? $data['method']->getMethods()
					: null)
				: ['GET']
		);
	}

	public function setRoute($route)
	{
		$this->route = $route;
	}

	public function getRoute()
	{
		return $this->route;
	}

	public function setMethod($method)
	{
		$this->method = $method;
	}

	public function getMethod()
	{
		return $this->method;
	}
}
