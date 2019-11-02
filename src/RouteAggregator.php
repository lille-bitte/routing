<?php

namespace LilleBitte\Routing;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class RouteAggregator
{
	/**
	 * @var array
	 */
	private $routes = [];

	/**
	 * @var RouteParser
	 */
	private $parser;

	public function __construct(RouteParser $parser = null)
	{
		$this->parser = $parser ?? new RouteParser;
	}

	public function addRoute(string $method, string $route, $handler)
	{
		$this->parser->parse($route);

		$this->routes[] = [
			'method' => $method,
			'route' => $this->parser->getSerializedSegments(),
			'placeholder' => $this->parser->getParameters(),
			'handler' => $handler
		];
	}

	public function get(string $route, $handler)
	{
		$this->addRoute('GET', $route, $handler);
	}

	public function post(string $route, $handler)
	{
		$this->addRoute('POST', $route, $handler);
	}

	public function put(string $route, $handler)
	{
		$this->addRoute('PUT', $route, $handler);
	}

	public function patch(string $route, $handler)
	{
		$this->addRoute('PATCH', $route, $handler);
	}

	public function delete(string $route, $handler)
	{
		$this->addRoute('DELETE', $route, $handler);
	}

	public function getRoutes()
	{
		return $this->routes;
	}
}
