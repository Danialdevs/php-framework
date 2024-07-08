<?php

namespace Danial\Framework\Routing;

use League\Container\Container;
use Danial\Framework\Controller\AbstractController;
use Danial\Framework\Http\Request;

class Router implements RouterInterface
{
    private array $routes;

    public function dispatch(Request $request, Container $container): array
    {
        $handler = $request->getRouteHandler();
        $vars = $request->getRouteArgs();

        if (is_array($handler)) {
            [$controllerId, $method] = $handler;
            $controller = $container->get($controllerId);

            if (is_subclass_of($controller, AbstractController::class)) {
                $controller->setRequest($request);
            }

            $handler = [$controller, $method];
        }

        return [$handler, $vars];
    }
}
