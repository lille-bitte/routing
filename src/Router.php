<?php

namespace LilleBitte\Routing;

use LilleBitte\Routing\Exception\DispatcherResolverException;
use LilleBitte\Routing\Exception\RouterException;
use Psr\Http\Message\RequestInterface;

use function filesize;
use function file_put_contents;
use function realpath;
use function var_export;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class Router
{
	use RouterTrait;

	/**
	 * @var Dispatcher
	 */
	private $dispatcher;

	/**
	 * @var RouteAggregator
	 */
	private $routeAggregator;

	/**
	 * @var array
	 */
	private $config;

	/**
	 * @var array
	 */
	private $callbacks = [];

	public function __construct(
		Dispatcher $dispatcher,
		RouteAggregator $routeAggregator
	) {
		$this->dispatcher = $dispatcher;
		$this->routeAggregator = $routeAggregator;
	}

	/**
	 * Register given route with more than one
	 * HTTP method.
	 *
	 * @param array $methods List of HTTP method.
	 * @param string $route Route path.
	 * @param mixed $handler Handler if route has matched.
	 * @return void
	 */
	public function any(array $methods, string $route, $handler)
	{
		$this->routeAggregator->addRoute(
			$methods,
			$route,
			$this->getHandlerId($handler)
		);
	}

	/**
	 * Register given route with GET HTTP method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Handler if route has matched.
	 * @return void
	 */
	public function get(string $route, $handler)
	{
		$this->routeAggregator->get(
			$route,
			$this->getHandlerId($handler)
		);
	}

	/**
	 * Register given route with POST HTTP method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Handler if route has matched.
	 * @return void
	 */
	public function post(string $route, $handler)
	{
		$this->routeAggregator->post(
			$route,
			$this->getHandlerId($handler)
		);
	}

	/**
	 * Register given route with PUT HTTP method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Handler if route has matched.
	 * @return void
	 */
	public function put(string $route, $handler)
	{
		$this->routeAggregator->put(
			$route,
			$this->getHandlerId($handler)
		);
	}

	/**
	 * Register given route with PATCH HTTP method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Handler if route has matched.
	 * @return void
	 */
	public function patch(string $route, $handler)
	{
		$this->routeAggregator->patch(
			$route,
			$this->getHandlerId($handler)
		);
	}

	/**
	 * Register given route with DELETE HTTP method.
	 *
	 * @param string $route Route path.
	 * @param mixed $handler Handler if route has matched.
	 * @return void
	 */
	public function delete(string $route, $handler)
	{
		$this->routeAggregator->delete(
			$route,
			$this->getHandlerId($handler)
		);
	}

	/**
	 * Dispatch matched route path with given
	 * HTTP method.
	 *
	 * @param string $method HTTP method.
	 * @param string $route Route path.
	 * @return array
	 */
	public function dispatch(string $method, string $route)
	{
		$this->assertWantCache();

		$wantCache = $this->getConfig('useCache');
		$cacheFile = $this->getConfig('cacheFile');

		if (true === $wantCache && file_exists($cacheFile) && filesize($cacheFile) !== 0) {
			$routes = require $cacheFile;

			foreach ($routes as $key => $value) {
				if ($value['route'] === $route && in_array($method, $value['methods'], true)) {
					return [
						'status' => $value['status'],
						'response' => $this->resolveCallback($value['handler'], $value['parameters'])
					];
				}
			}
		}

		$this->dispatcher->setAggregator($this->routeAggregator);

		$ret = $this->dispatcher->dispatch($method, $route);

		if ($ret['status'] === Dispatcher::NOT_FOUND ||
			$ret['status'] === Dispatcher::METHOD_NOT_ALLOWED) {
			return $ret;
		}

		if ($wantCache) {
			$cached = isset($routes)
				? $routes
				: [];

			$cached[] = $ret;

			file_put_contents(
				$cacheFile,
				'<?php return ' . var_export($cached, true) . ';'
			);
		}

		return [
			'status' => $ret['status'],
			'response' => $this->resolveCallback($ret['handler'], $ret['parameters'])
		];
	}

	/**
	 * Dispatch encapsulated HTTP request.
	 *
	 * @param RequestInterface $request Encapsulated HTTP request.
	 * @return array
	 */
	public function dispatchRequest(RequestInterface $request)
	{
		return $this->dispatch($request->getMethod(), $request->getUri()->getPath());
	}

	/**
	 * Set route configuration.
	 *
	 * @param array $config Configuration list.
	 * @return void
	 */
	public function setConfig(array $config)
	{
		$this->config = $config;
	}

	/**
	 * Get route configuration.
	 *
	 * @return array|string|null
	 */
	public function getConfig($name = null)
	{
		return null === $name
			? $this->config
			: ($this->hasConfig($name)
				? $this->config[$name]
				: null);
	}

	/**
	 * Check if configuration array has
	 * value with given key.
	 *
	 * @param string $name Configuration key.
	 * @return boolean
	 */
	public function hasConfig(string $name)
	{
		return isset($this->config[$name]);
	}

	/**
	 * Get list of registered callbacks.
	 *
	 * @return array
	 */
	public function getCallbacks()
	{
		return $this->callbacks;
	}

	/**
	 * Add callback into callbacks list.
	 *
	 * @param Closure $callback Callback.
	 * @return void
	 */
	public function addCallback(\Closure $callback)
	{
		$key = sprintf(
			"handler%d",
			!count($this->callbacks) ? 0 : count($this->callbacks)
		);

		$this->callbacks[$key] = $callback;
	}

	/**
	 * Check if callback with given key
	 * exists.
	 *
	 * @param string $id Callback key.
	 * @return boolean
	 */
	public function hasCallback(string $id)
	{
		return array_key_exists($id, $this->callbacks);
	}

	/**
	 * Get callback with given key.
	 *
	 * @param string $id Callback key.
	 * @return Closure
	 */
	public function getCallback(string $id)
	{
		if (!$this->hasCallback($id)) {
			throw new DispatcherResolverException(
				sprintf(
					"Callback with id '%' not exist.",
					$id
				)
			);
		}

		return $this->callbacks[$id];
	}
}
