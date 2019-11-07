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
	 * @param array $routes List of all route metadata.
	 * @return string
	 */
	private function getRegex(array $routes)
	{
		$regex = '~^(?|';
		$groups = [];

		foreach ($routes as $metadata) {
			if (empty($metadata['route']) && empty($metadata['placeholder'])) {
				$groups[] = '/';
				continue;
			}

			$tmp = '';
			$index = 0;
			$total = count($metadata['route']) + count($metadata['placeholder']);

			for ($i = 0, $j = 0; $index < $total; ) {
				if (isset($metadata['route'][$i]) &&
					$metadata['route'][$i]['index'] === $index) {
					$tmp .= '/' . $metadata['route'][$i]['value'];
					$i++;
				}

				if (isset($metadata['placeholder'][$j]) &&
					$metadata['placeholder'][$j]['index'] === $index) {
					$tmp .= '/(' . $metadata['placeholder'][$j]['default'] . ')';
					$j++;
				}

				$index++;
			}

			$groups[] = $tmp;
		}

		$regex .= join('|', array_values($groups)) . ')$~x';
		return $regex;
	}

	/**
	 * Match given route and assign current position and
	 * (if any) route parameters.
	 *
	 * @param array $routes Routes metadata.
	 * @param string $route Route path.
	 * @param string $method Route method.
	 * @param array $pos List of position in metadata aggregator.
	 * @param array $routeParams Collected route parameters.
	 * @param array $allowedMethods Collected HTTP methods which allowed.
	 * @return boolean
	 */
	private function match(
		array $routes,
		string $route,
		string $method,
		array &$pos,
		array &$routeParams,
		array &$allowedMethods
	) {
		// reset list reference
		$pos = $routeParams = $allowedMethods = [];

		if (!preg_match($this->getRegex($routes), $route, $res)) {
			return false;
		}

		$matchByPosition = function($q, $staticRoute) {
			$full = explode('/', ltrim($q, '/'));
			$tmp = 0;

			foreach ($staticRoute as $value) {
				if (isset($full[$value['index']]) && $value['value'] === $full[$value['index']]) {
					$tmp++;
				}
			}

			return !$tmp ? null : (count($full) - $tmp);
		};

		$collectMetadataValue = function($q, string $directive) {
			$val = [];

			foreach ($q[$directive] as $key => $value) {
				$val[] = $value['value'];
			}

			return $val;
		};

		foreach ($routes as $key => $value) {
			$tmp = '/' . join('/', $collectMetadataValue($value, 'route'));

			if ($route === $tmp) {
				$pos[] = $key;
				$allowedMethods = array_merge($allowedMethods, $value['method']);
				$routeParams[] = [];
			}

			if (count($value['placeholder']) === $matchByPosition($res[0], $value['route']) &&
		        $this->validateRouteParameters($res, $value['placeholder'])) {
				$pos[] = $key;
				$allowedMethods = array_merge($allowedMethods, $value['method']);
				$routeParams[] = array_combine(
					$collectMetadataValue($value, 'placeholder'),
					array_slice($res, 1)
				);
			}
		}

		return true;
	}

	/**
	 * Validate matched route parameters.
	 *
	 * @param array $values Route parameter values.
	 * @param array $metadata Route parameter metadata.
	 * @return boolean
	 */
	private function validateRouteParameters(array $values, array $metadata)
	{
		$values = array_values(array_slice($values, 1));

		foreach ($metadata as $key => $val) {
			if (!isset($values[$key])) {
				return false;
			}

			$pattern = isset($val['pattern'])
				? $val['pattern']
				: $val['default'];

			if (!preg_match('~^' . $pattern . '$~x', $values[$key])) {
				return false;
			}
		}

		return true;
	}
}
