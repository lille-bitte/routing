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

	/**
	 * Set route aggregator.
	 *
	 * @param RouteAggregator|null $routeAggregator Route aggregator instance.
	 * @return void
	 */
	public function setAggregator(RouteAggregator $routeAggregator = null)
	{
		$this->routeAggregator = $routeAggregator;
	}

	/**
	 * Get route aggregator.
	 *
	 * @return RouteAggregator|null
	 */
	public function getAggregator()
	{
		return $this->routeAggregator;
	}

	/**
	 * Dispatch matched route with supplied method.
	 *
	 * @param string $method Route HTTP method.
	 * @param string $route Route path.
	 * @return array
	 */
	public function dispatch(string $method, string $route)
	{
		$routes = $this->routeAggregator->getRoutes();
		$allowedMethods = [];
		$handlerParams = [];
		$position = [];

		$res = $this->match(
			$routes,
			$route,
			$method,
			$position,
			$handlerParams,
			$allowedMethods
		);

		if (false === $res) {
			return ['status' => self::NOT_FOUND];
		}

		foreach ($allowedMethods as $key => $value) {
			if (false !== array_search($method, $value, true)) {
				$current = $key;
				break;
			}
		}

		if (!isset($current)) {
			return [
				'status' => self::METHOD_NOT_ALLOWED,
				'allowed-methods' => $allowedMethods
			];
		}

		return [
			'status' => self::FOUND,
			'route' => $route,
			'methods' => $routes[$position[$current]]['method'],
			'handler' => $routes[$position[$current]]['handler'],
			'parameters' => $handlerParams[$current]
		];
	}
}
