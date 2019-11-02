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
	 * @param string $method Route method.
	 * @param string $route Route path.
	 * @param mixed $handler Route handler.
	 * @return void
	 */
	public function addRoute(string $method, string $route, $handler)
	{
		$this->parser->reset();
		$this->parser->parse($route);

		$this->routes[] = [
			'method' => $method,
			'route' => $this->parser->getSerializedSegments(),
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
	public function get(string $route, $handler)
	{
		$this->addRoute('GET', $route, $handler);
	}

	/**
	 * Add URI route with POST http method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Route handler.
	 * @return void
	 */
	public function post(string $route, $handler)
	{
		$this->addRoute('POST', $route, $handler);
	}

	/**
	 * Add URI route with PUT http method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Route handler.
	 * @return void
	 */
	public function put(string $route, $handler)
	{
		$this->addRoute('PUT', $route, $handler);
	}

	/**
	 * Add URI route with PATCH http method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Route handler.
	 * @return void
	 */
	public function patch(string $route, $handler)
	{
		$this->addRoute('PATCH', $route, $handler);
	}

	/**
	 * Add URI route with DELETE http method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Route handler.
	 * @return void
	 */
	public function delete(string $route, $handler)
	{
		$this->addRoute('DELETE', $route, $handler);
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
}
