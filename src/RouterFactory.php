<?php

namespace LilleBitte\Routing;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class RouterFactory
{
    /**
     * Get router instance.
     *
     * @return RouterInterface
     */
    public static function getRouter()
    {
        return new Router(new Dispatcher, new RouteAggregator);
    }
}
