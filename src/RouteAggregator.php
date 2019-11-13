<?php

namespace LilleBitte\Routing;

use function rtrim;

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

	/**
	 * @var string
	 */
	private $group = '';

	public function __construct(RouteParser $parser = null)
	{
		$this->parser = $parser ?? new RouteParser;
	}

	/**
	 * Add URI route with method and handler.
	 *
	 * @param array $method Route method.
	 * @param string $route Route path.
	 * @param mixed $handler Route handler.
	 * @return void
	 */
	public function addRoute(
		array $methods,
		string $route,
		$handler,
		array $pattern = []
	) {
		$this->parser->reset();
		$this->parser->parse(
			sprintf("%s/%s", $this->getGroup(), $route),
			$pattern
		);

		$this->routes[] = [
			'method' => $methods,
			'route' => $this->parser->getSegments(),
			'placeholder' => $this->parser->getParameters(),
			'handler' => $handler
		];
	}

	/**
	 * Return aggregated HTTP route.
	 *
	 * @return array
	 */
	public function getRoutes()
	{
		return $this->routes;
	}

	/**
	 * Set routes metadata.
	 *
	 * @param array $routes Routes metadata.
	 * @return void
	 */
	public function setRoutes(array $routes)
	{
		$this->routes = $routes;
	}

	/**
	 * Set route group.
	 *
	 * @param string $group Route group.
	 * @return void
	 */
	public function setGroup(string $group)
	{
		$this->group = rtrim($group, '/');
	}

	/**
	 * Get route group.
	 *
	 * @return string
	 */
	public function getGroup()
	{
		return $this->group;
	}
}
