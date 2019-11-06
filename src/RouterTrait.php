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
	private function resolveCallback(callable $callback, array $parameters): ResponseInterface
	{
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

	private function resolveMethod(
		$class,
		string $method,
		array $parameters
	): ResponseInterface {
	}
}
