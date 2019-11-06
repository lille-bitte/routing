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
		$this->assertWantCache();
		$this->dispatcher = $dispatcher;
		$this->routeAggregator = $routeAggregator;
	}

	public function any(array $methods, string $route, $handler)
	{
		$this->routeAggregator->addRoute($methods, $route, $handler);
	}

	public function get(string $route, $handler)
	{
		$this->routeAggregator->get($route, $handler);
	}

	public function post(string $route, $handler)
	{
		$this->routeAggregator->post($route, $handler);
	}

	public function put(string $route, $handler)
	{
		$this->routeAggregator->put($route, $handler);
	}

	public function delete(string $route, $handler)
	{
		$this->routeAggregator->delete($route, $handler);
	}

	public function dispatch(string $method, string $route)
	{
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

	public function dispatchRequest(RequestInterface $request)
	{
		return $this->dispatch($request->getMethod(), $request->getUri()->getPath());
	}

	public function setConfig(array $config)
	{
		$this->config = $config;
	}

	public function getConfig($name = null)
	{
		return null === $name
			? $this->config
			: ($this->hasConfig($name)
				? $this->config[$name]
				: null);
	}

	public function hasConfig(string $name)
	{
		return isset($this->config[$name]);
	}

	public function getCallbacks()
	{
		return $this->callbacks;
	}

	public function addCallback(\Closure $callback)
	{
		$key = sprintf(
			"handler%d",
			!count($this->callback) ? 0 : count($this->callback)
		);

		$this->callback[$key] = $callback;
	}

	public function hasCallback(string $id)
	{
		return array_key_exists($id, $this->callback);
	}

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

		return $this->callback[$id];
	}
}
