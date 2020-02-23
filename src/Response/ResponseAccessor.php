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
		return $this->response['status'];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResponse()
	{
		return $this->response['response'];
	}

	/**
	 * Set dispatched response.
	 *
	 * @param array $response Dispatched response.
	 * @return void
	 */
	private function setDispatchedResponse(array $response)
	{
		$this->assertKeyExistence($response);
		$this->response = $response;
	}

	/**
	 * Assert dispatched response.
	 *
	 * @param array $response Dispatched response.
	 * @return void
	 */
	private function assertKeyExistence(array $response)
	{
		if (!isset($response['status']) || !isset($response['response'])) {
			throw new DispatchedResponseException(
				"Both 'status' and 'response' must be exist in " .
				"dispatched response array."
			);
		}
	}
}
