<?php

namespace LilleBitte\Routing;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface RouterInterface
{
	/**
	 * Register given route with more than one
	 * HTTP method.
	 *
	 * @param array $methods List of HTTP method.
	 * @param string $route Route path.
	 * @param mixed $handler Handler if route has matched.
	 * @return void
	 */
	public function any(array $methods, string $route, $handler, array $pattern = []);

	/**
	 * Register given route with GET HTTP method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Handler if route has matched.
	 * @return void
	 */
	public function get(string $route, $handler, array $pattern = []);

	/**
	 * Register given route with POST HTTP method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Handler if route has matched.
	 * @return void
	 */
	public function post(string $route, $handler, array $pattern = []);

	/**
	 * Register given route with PUT HTTP method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Handler if route has matched.
	 * @return void
	 */
	public function put(string $route, $handler, array $pattern = []);

	/**
	 * Register given route with PATCH HTTP method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Handler if route has matched.
	 * @return void
	 */
	public function patch(string $route, $handler, array $pattern = []);

	/**
	 * Register given route with DELETE HTTP method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Handler if route has matched.
	 * @return void
	 */
	public function delete(string $route, $handler, array $pattern = []);

	/**
	 * Group list of routes by route prefix.
	 *
	 * @param string $group Group prefix.
	 * @param callable $callback Callback.
	 * @return void
	 */
	public function group(string $group, callable $callback);

	/**
	 * Dispatch matched route path with given
	 * HTTP method.
	 *
	 * @param string $method HTTP method.
	 * @param string $route Route path.
	 * @return array
	 */
	public function dispatch(string $method, string $route);
}
