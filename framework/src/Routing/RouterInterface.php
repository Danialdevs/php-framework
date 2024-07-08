<?php

namespace Danial\Framework\Routing;

use League\Container\Container;
use Danial\Framework\Http\Request;

interface RouterInterface
{
    public function dispatch(Request $request, Container $container);
}
