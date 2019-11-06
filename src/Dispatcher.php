<?php

namespace LilleBitte\Routing;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class Dispatcher
{
	use DispatcherTrait;

	/**
	 * @var RouteAggregator
	 */
	private $routeAggregator;

	const FOUND = 0;
	const METHOD_NOT_ALLOWED = 1;
	const NOT_FOUND = 2;

	public function __construct(RouteAggregator $routeAggregator = null)
	{
		$this->setAggregator($routeAggregator);
	}

	public function setAggregator(RouteAggregator $routeAggregator = null)
	{
		$this->routeAggregator = $routeAggregator;
	}

	public function getAggregator()
	{
		return $this->routeAggregator;
	}

	public function dispatch(string $method, string $route)
	{
		$routes = $this->routeAggregator->getRoutes();
		$handlerParams = [];
		$position = 0;

		if (false === $this->match($routes, $route, $method, $position, $handlerParams)) {
			return ['status' => self::NOT_FOUND];
		}

		if (!in_array($method, $routes[$position]['method'], true)) {
			return [
				'status' => self::METHOD_NOT_ALLOWED,
				'allowed-methods' => $routes[$position]['method']
			];
		}

		return [
			'status' => self::FOUND,
			'route' => $route,
			'methods' => $routes[$position]['method'],
			'handler' => $routes[$position]['handler'],
			'parameters' => $handlerParams
		];
	}
}
