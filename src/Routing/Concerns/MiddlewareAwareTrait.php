<?php declare(strict_types=1);

namespace tiFy\Routing\Concerns;

use tiFy\Contracts\Routing\Router;
use League\Route\Middleware\MiddlewareAwareInterface;

trait MiddlewareAwareTrait
{
    /**
     * {@inheritDoc}
     *
     * @return MiddlewareAwareTrait
     */
    public function middleware($middleware): MiddlewareAwareInterface
    {
        if (is_string($middleware)) {
            $router = $this->collection ?? $this;

            if ($router instanceof Router) {
                $middleware = $router->getNamedMiddleware($middleware);
            }
        } elseif (is_array($middleware)) {
            foreach ($middleware as $item) {
                $this->middleware($item);
            }

            return $this;
        }

        return parent::middleware($middleware);
    }

    /**
     * Add a middleware as a class name to the stack
     *
     * @param string $middleware
     *
     * @return MiddlewareAwareTrait|MiddlewareAwareInterface
     */
    public function lazyMiddleware(string $middleware): MiddlewareAwareInterface
    {
        $this->middleware[] = $this->getContainer()->get($middleware);

        return $this;
    }

    /**
     * Add multiple middlewares as class names to the stack
     *
     * @param string[] $middlewares
     *
     * @return MiddlewareAwareTrait|MiddlewareAwareInterface
     */
    public function lazyMiddlewares(array $middlewares): MiddlewareAwareInterface
    {
        foreach ($middlewares as $middleware) {
            $this->lazyMiddleware($middleware);
        }

        return $this;
    }
}