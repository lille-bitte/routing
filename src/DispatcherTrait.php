<?php

namespace LilleBitte\Routing;

use function array_keys;
use function array_slice;
use function array_combine;
use function explode;
use function count;
use function join;
use function ltrim;
use function rtrim;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
trait DispatcherTrait
{
	/**
	 * Grouping parted regex into big one.
	 * https://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
	 *
	 * @param array $metadata Route metadata.
	 * @param string $route Route URI.
	 * @param array $param Marshalled handler parameters.
	 * @return boolean
	 */
	private function assertAndResolvePlaceholder(array $metadata, string $route, array &$param)
	{
		if (!empty($param)) {
			$param = [];
		}

		$routes = [];

		foreach ($metadata['route'] as $key => $value) {
			$routes[] = $value['value'];
		}

		$staticRoutes = empty($routes)
			? '/'
			: '/' . join('/', $routes);

		$patterns = [];

		foreach ($metadata['placeholder'] as $key => $value) {
			$patterns[] = $value['pattern'];
			$param[$value['value']] = null;
		}

		$placeholderPattern = empty($patterns)
			? ''
			: '/' . sprintf('(%s)', join(')/(', $patterns));

		$generated = '~' . $staticRoutes . $placeholderPattern . '~i';

		preg_match($generated, $route, $res);

		if (count($res) !== count($patterns) + 1) {
			return false;
		}

		$param = array_combine(array_keys($param), array_slice($res, 1));

		return true;
	}
}
