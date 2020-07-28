<?php

declare(strict_types=1);

namespace LilleBitte\Routing\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface ResponseAccessorInterface
{
    /**
     * Get dispatched response status.
     *
     * @return integer
     */
    public function getStatus();

    /**
     * Get dispatched response object.
     *
     * @return ResponseInterface
     */
    public function getResponse();
}
