<?php

namespace Danial\Framework\Http;

use League\Container\Container;
use Danial\Framework\Event\EventDispatcher;
use Danial\Framework\Http\Events\ResponseEvent;
use Danial\Framework\Http\Exceptions\HttpException;
use Danial\Framework\Http\Middleware\RequestHandlerInterface;

class Kernel
{
    private string $appEnv = 'local';

    public function __construct(
        private readonly Container $container,
        private readonly RequestHandlerInterface $requestHandler,
        private readonly EventDispatcher $eventDispatcher
    ) {
        $this->appEnv = $this->container->get('APP_ENV');
    }

    public function handle(Request $request): Response
    {
        try {
            $response = $this->requestHandler->handle($request);
        } catch (\Exception $e) {
            $response = $this->createExceptionResponse($e);
        }

        // $response->setStatusCode(500);

        $this->eventDispatcher->dispatch(new ResponseEvent($request, $response));

        return $response;
    }

    public function terminate(Request $request, Response $response): void
    {
        $request->getSession()?->clearFlash();
    }

    private function createExceptionResponse(\Exception $e): Response
    {
        if (in_array($this->appEnv, ['local', 'testing'])) {
            throw $e;
        }

        if ($e instanceof HttpException) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }

        return new Response('Server error', 500);
    }
}
