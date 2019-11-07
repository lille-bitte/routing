<?php

declare(strict_types=1);

namespace LilleBitte\Routing;

use LilleBitte\Routing\Exception\MatcherException;

use function in_array;
use function preg_split;
use function join;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class RouteParser
{
	/**
	 * @var array
	 */
	private $segments = [];

	/**
	 * @var array
	 */
	private $parameters = [];

	/**
	 * @var string
	 */
	private const REGEX_SPLITTER = '/([a-z0-9\-\_][a-z0-9\-\_]*)|([^\{\}\s\/]+)|\s*|(.)/x';

	/**
	 * @var string
	 */
	private const DEFAULT_PLACEHOLDER_REGEX = '[^/]+';

	/**
	 * Parse given route to two tuples, static routes and
	 * route parameters.
	 *
	 * @param string $route Route path.
	 * @param array $pattern Route parameters pattern.
	 * @return void
	 */
	public function parse(string $route, array $pattern = [])
	{
		$res = preg_split(
			self::REGEX_SPLITTER,
			$route,
			-1,
			PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
		);

		$pos = $index = 0;

		while (isset($res[$pos])) {
			if ($res[$pos] === '/' || $res[$pos] === '}') {
				$pos++;
				continue;
			}

			if ($res[$pos] === '{') {
				if ($res[$pos + 1] === '}') {
					throw new MatcherException(
						"Placeholder must not be empty."
					);
				}

				$val = $res[$pos + 1];

				$this->parameters[] = [
					'index' => $index++,
					'value' => $val,
					'pattern' => isset($pattern[$val])
						? $pattern[$val]
						: self::DEFAULT_PLACEHOLDER_REGEX
				];

				$pos += 2;
				continue;
			}

			$this->segments[] = [
				'index' => $index++,
				'value' => $res[$pos]
			];

			$pos++;
		}
	}

	/**
	 * Reset parser state.
	 *
	 * @return void
	 */
	public function reset()
	{
		$this->segments = $this->parameters = [];
	}

	/**
	 * Get route static segments
	 * metadata.
	 *
	 * @return array
	 */
	public function getSegments()
	{
		return $this->segments;
	}

	/**
	 * Get route parameters metadata.
	 *
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}
}
