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
		$this->parser->parse($route, $pattern);

		$this->routes[] = [
			'method' => $methods,
			'route' => $this->parser->getSegments(),
			'placeholder' => $this->parser->getParameters(),
			'handler' => $handler
		];
	}

	/**
	 * Add URI route with GET http method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Route handler.
	 * @return void
	 */
	public function get(string $route, $handler, array $pattern = [])
	{
		$this->addRoute(['GET'], $route, $handler, $pattern);
	}

	/**
	 * Add URI route with POST http method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Route handler.
	 * @return void
	 */
	public function post(string $route, $handler, array $pattern = [])
	{
		$this->addRoute(['POST'], $route, $handler, $pattern);
	}

	/**
	 * Add URI route with PUT http method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Route handler.
	 * @return void
	 */
	public function put(string $route, $handler, array $pattern = [])
	{
		$this->addRoute(['PUT'], $route, $handler, $pattern);
	}

	/**
	 * Add URI route with PATCH http method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Route handler.
	 * @return void
	 */
	public function patch(string $route, $handler, array $pattern = [])
	{
		$this->addRoute(['PATCH'], $route, $handler, $pattern);
	}

	/**
	 * Add URI route with DELETE http method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Route handler.
	 * @return void
	 */
	public function delete(string $route, $handler, array $pattern = [])
	{
		$this->addRoute(['DELETE'], $route, $handler, $pattern);
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
}
