<?php

namespace LilleBitte\Routing;

use Psr\Http\Message\ResponseInterface;
use LilleBitte\Routing\Exception\DispatcherResolverException;
use ReflectionFunction;

use function count;
use function call_user_func_array;
use function sprintf;
use function array_keys;
use function array_values;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
trait RouterTrait
{
	/**
	 * Resolve given callback with given
	 * set of parameters.
	 *
	 * @param mixed $callback Callback.
	 * @param array $parameters Callback parameters.
	 * @return ResponseInterface
	 */
	private function resolveCallback($callback, array $parameters): ResponseInterface
	{
		if (!is_callable($callback) && !$this->hasCallback($callback)) {
			throw new DispatcherResolverException(
				sprintf(
					"Callback with id '%s' not exist.",
					$callback
				)
			);
		}

		$callback = is_callable($callback)
			? $callback
			: $this->getCallback($callback);

		$refCallback = new ReflectionFunction($callback);
		$res = $refCallback->getParameters();

		if (count($res) !== count($parameters)) {
			throw new DispatcherResolverException(
				sprintf(
					"Number of route parameters is different than callback parameters (" .
					"route params: %d, callback params: %d)",
					count($parameters),
					count($res)
				)
			);
		}

		$routeParams = array_keys($parameters);

		foreach ($res as $key => $value) {
			if ($value->getName() === $routeParams[$key]) {
				continue;
			}

			throw new DispatcherResolverException(
				sprintf(
					"Route parameters in position (%d) must be (%s), but in " .
					"callback got (%s)",
					$key,
					$routeParams[$key],
					$value->getName()
				)
			);
		}

		return call_user_func_array($callback, array_values($parameters));
	}

	/**
	 * Check if cache-related configuration
	 * are properly set.
	 */
	private function assertWantCache()
	{
		$wantCache = $this->getConfig('useCache');
		$cacheFile = $this->getConfig('cacheFile');

		if (((null === $wantCache || false === $wantCache) && null !== $cacheFile) ||
	        (null === $cacheFile && (null !== $wantCache && $wantCache))) {
			throw new RouterException(
				"'cacheFile' has been set. But, 'useCache' is false. Or 'useCache' is true but " .
				"'cacheFile' has not set. Check your config."
			);
		}
	}

	/**
	 * Get handler identifier to pass on
	 * route registration method.
	 *
	 * @param mixed $handler Route handler.
	 * @return string
	 */
	private function getHandlerId($handler)
	{
		if ($handler instanceof \Closure) {
			$this->addCallback($handler);
			$handler = array_keys($this->callbacks)[count($this->callbacks) - 1];
		}

		return $handler;
	}

	private function resolveMethod(
		$class,
		string $method,
		array $parameters
	): ResponseInterface {
	}
}
