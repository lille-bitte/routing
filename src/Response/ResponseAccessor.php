<?php

namespace LilleBitte\Routing\Response;

use LilleBitte\Routing\Exception\DispatchedResponseException;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class ResponseAccessor implements ResponseAccessorInterface
{
	/**
	 * @var array
	 */
	private $response;

	public function __construct(array $response)
	{
		$this->setDispatchedResponse($response);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getStatus()
	{
		return isset($this->response['status'])
			? $this->response['status'];
			: null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResponse()
	{
		return isset($this->response['response'])
			? $this->response['response']
			: null;
	}

	/**
	 * Set dispatched response.
	 *
	 * @param array $response Dispatched response.
	 * @return void
	 */
	private function setDispatchedResponse(array $response)
	{
		$this->response = $response;
	}
}
