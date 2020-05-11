<?php declare(strict_types=1);

namespace tiFy\Routing;

use ArrayIterator;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use League\Route\{Middleware\MiddlewareAwareInterface,
    Route as LeagueRoute,
    RouteGroup as LeagueRouteGroup,
    Router as LeagueRouter
};
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\{ResponseInterface as Response, ServerRequestInterface as Request};
use Psr\Http\Server\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Response as SfResponse;
use tiFy\Contracts\Container\Container;
use tiFy\Contracts\Routing\{Route as RouteContract, RouteGroup as RouteGroupContract, Router as RouterContract};
use tiFy\Http\Response as HttpResponse;
use tiFy\Routing\Concerns\{ContainerAwareTrait, RegisterMapAwareTrait, RouteCollectionAwareTrait};
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;

class Router extends LeagueRouter implements RouterContract
{
    use ContainerAwareTrait, RegisterMapAwareTrait, RouteCollectionAwareTrait;

    /**
     * Instance de la route associée à la requête HTTP courante.
     * @var Route
     */
    protected $current;

    /**
     * Liste des routes déclarées.
     * @var Route[]
     */
    protected $items = [];

    /**
     * Liste des controleurs déclarés et qualifiés.
     * @var BaseController[]|array
     */
    protected $namedController = [];

    /**
     * Liste des middlewares déclarés et qualifiés.
     * @var MiddlewareInterface[]|array
     */
    protected $namedMiddleware = [];

    /**
     * Préfixe des chemins des routes.
     * @var string|null
     */
    protected $prefix;

    /**
     * @inheritdoc
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @inheritdoc
     */
    public function collect(): Collection
    {
        return new Collection($this->items);
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @inheritdoc
     */
    public function current(): ?RouteContract
    {
        return $this->current = $this->current ?? $this->collect()->first(function (Route $item) {
                return $item->isCurrent();
            });
    }

    /**
     * @inheritdoc
     */
    public function currentRouteName(): ?string
    {
        return $this->hasCurrent() ? $this->current()->getName() : null;
    }

    /**
     * @inheritdoc
     */
    public function dispatch(Request $request): Response
    {
        if (is_null($this->getStrategy())) {
            $this->setStrategy($this->getContainer()->get('router.strategy.default'));
        }
        return parent::dispatch($request);
    }

    /**
     * @inheritDoc
     */
    public function emit($response): void
    {
        if ($response instanceof Response) {
            /** @var EmitterInterface $emitter */
            $emitter = $this->getContainer()->get('router.emitter');
            $emitter->emit($response);

            events()->trigger('router.emit.response', [$response]);
        } elseif ($response instanceof SfResponse) {
            $response->send();

            events()->trigger('router.emit.response', [HttpResponse::convertToPsr($response)]);
        } else {
            (new HttpResponse(__('La réponse attendue n\'est pas conforme', 'tify'), 500))->send();
        }
    }

    /**
     * @inheritdoc
     */
    public function exists(): bool
    {
        return !empty($this->items);
    }

    /**
     * {@inheritDoc}
     *
     * @return RouteGroupContract
     */
    public function group(string $prefix, callable $group): LeagueRouteGroup
    {
        $group = $this->getContainer()
            ? $this->getContainer()->get(RouteGroupContract::class, [$prefix, $group, $this])
            : new RouteGroup($prefix, $group, $this);

        $this->groups[] = $group;

        return $group;
    }

    /**
     * {@inheritDoc}
     *
     * @return Container|null
     */
    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function getNamedController(string $name)
    {
        return $this->namedController[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getNamedMiddleware(string $name): ?MiddlewareInterface
    {
        return $this->namedMiddleware[$name] ?? null;
    }

    /**
     * {@inheritDoc}
     *
     * @return RouteContract
     */
    public function getNamedRoute(string $name): LeagueRoute
    {
        return parent::getNamedRoute($name);
    }

    /**
     * @inheritdoc
     */
    public function hasCurrent(): bool
    {
        return $this->current() instanceof Route;
    }

    /**
     * @inheritdoc
     */
    public function hasNamedRoute(string $name): bool
    {
        return !!$this->collect()->first(function (Route $item) use ($name) {
            return $item->getName() === $name;
        });
    }

    /**
     * @inheritdoc
     */
    public function isCurrentNamed(string $name): bool
    {
        return $this->currentRouteName() === $name;
    }

    /**
     * @inheritdoc
     *
     * @return RouteContract
     */
    public function map(string $method, string $path, $handler): LeagueRoute
    {
        $prefix = ltrim(rtrim(is_null($this->prefix) ? url()->rewriteBase() : $this->prefix, '/'), '/');

        $path = '/' . ltrim($prefix . sprintf('/%s', ltrim($path, '/')), '/');

        if (is_string($handler)) {
            if (preg_match('/([a-zA-Z1-9_\-.]+)@([a-zA-Z1-9_]+)/', $handler, $match)) {
                if ($controller = $this->getNamedController($match[1])) {
                    $handler = [$controller, $match[2]];
                }
            } elseif ($controller = $this->getNamedController($handler)) {
                $handler = $controller;
            }
        } elseif (is_array($handler) && !empty($handler[0]) && is_string($handler[0])) {
            if ($controller = $this->getNamedController($handler[0])) {
                $handler = [$controller, $handler[1]];
            }
        }

        $route = $this->getContainer()
            ? $this->getContainer()->get(RouteContract::class, [$method, $path, $handler, $this])
            : new Route($method, $path, $handler, $this);

        $this->routes[] = $route;

        return $route;
    }

    /**
     * @inheritDoc
     */
    public function middleware($middleware): MiddlewareAwareInterface
    {
        if (is_string($middleware)) {
            $middleware = $this->getNamedMiddleware($middleware);
        } elseif (is_array($middleware)) {
            foreach ($middleware as $item) {
                $this->middleware($item);
            }

            return $this;
        }

        return parent::middleware($middleware);
    }

    /**
     * @inheritdoc
     */
    public function parseRoutePath(string $path): string
    {
        return preg_replace(array_keys($this->patternMatchers), array_values($this->patternMatchers), $path);
    }

    /**
     * Récupération de l'itérateur.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Vérifie l'existance d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Récupération de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    /**
     * Définition de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     * @param mixed $value Valeur à définir.
     *
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Suppression de la valeur d'un attribut selon une clé d'indice.
     *
     * @param mixed $key Clé d'indice.
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->items[$key]);
    }

    /**
     * @inheritdoc
     */
    protected function prepRoutes(Request $request): void
    {
        $this->processGroups($request);
        $this->buildNameIndex();

        /** @var Route[] $routes */
        $this->items = $routes = array_merge(array_values($this->routes), array_values($this->namedRoutes));

        foreach ($routes as $key => $route) {
            if (!is_null($route->getScheme()) && $route->getScheme() !== $request->getUri()->getScheme()) {
                continue;
            }

            if (!is_null($route->getHost()) && $route->getHost() !== $request->getUri()->getHost()) {
                continue;
            }

            if (!is_null($route->getPort()) && $route->getPort() !== $request->getUri()->getPort()) {
                continue;
            }

            if (is_null($route->getStrategy())) {
                if (($group = $route->getParentGroup()) && !is_null($group->getStrategy())) {
                    $route->setStrategy($group->getStrategy());
                } else {
                    $route->setStrategy($this->getStrategy());
                }
            }

            $this->addRoute($route->getMethod(), $this->parseRoutePath($route->getPath()), $route);
        }
    }

    /**
     * @inheritDoc
     */
    public function registerController(string $name, $controller)
    {
        return $this->namedController[$name] = $controller;
    }

    /**
     * @inheritDoc
     */
    public function registerMiddleware(string $name, MiddlewareInterface $middleware): MiddlewareInterface
    {
        return $this->namedMiddleware[$name] = $middleware;
    }

    /**
     * @inheritDoc
     */
    public function setControllerStack(array $controllers): RouterContract
    {
        foreach ($controllers as $name => $controller) {
            $this->registerController($name, $controller);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMiddlewareStack(array $middlewares): RouterContract
    {
        foreach ($middlewares as $name => $middleware) {
            if (is_string($name) && $middleware instanceof MiddlewareInterface) {
                $this->registerMiddleware($name, $middleware);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPrefix(?string $prefix = null): RouterContract
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function url(string $name, array $parameters = [], bool $absolute = false, bool $asserts = false): ?string
    {
        try {
            $route = $this->getNamedRoute($name);

            try {
                return $route->getUrl($parameters, $absolute);
            } catch (LogicException $e) {
                if ($asserts) {
                    throw new LogicException($e->getMessage(), $e->getCode());
                } else {
                    return null;
                }
            }
        } catch (InvalidArgumentException $e) {
            if ($asserts) {
                throw new InvalidArgumentException($e->getMessage(), $e->getCode());
            } else {
                return null;
            }
        }
    }
}