<?php

namespace LilleBitte\Routing\Annotation;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class Method
{
	/**
	 * @var array
	 */
	private $methods;

	public function __construct($methods = [])
	{
		$this->setMethods($methods);
	}

	public function addMethod(string $method)
	{
		$this->methods[] = $method;
	}

	public function setMethods(array $methods)
	{
		$this->methods = $methods;
	}

	public function getMethods()
	{
		return $this->methods;
	}
}
