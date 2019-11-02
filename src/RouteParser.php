<?php

declare(strict_types=1);

namespace LilleBitte\Routing;

use LilleBitte\Routing\Exception\MatcherException;

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
	private const REGEX_SPLITTER = '/([a-z\-\_][a-z\-\_]*)|([^\{\}\s\/]+)|\s*|(.)/ix';

	public function parse(string $route)
	{
		$res = preg_split(
			self::REGEX_SPLITTER,
			$route,
			-1,
			PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
		);

		$pos = 0;

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

				$this->parameters[] = $res[$pos + 1];
				$pos += 2;
				continue;
			}

			$this->segments[] = $res[$pos];
			$pos++;
		}
	}

	public function getSegments()
	{
		return $this->segments;
	}

	public function getSerializedSegments()
	{
		return join('/', $this->getSegments());
	}

	public function getParameters()
	{
		return $this->parameters;
	}
}
