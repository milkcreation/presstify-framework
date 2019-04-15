<?php declare(strict_types=1);

namespace tiFy\Routing;

use ArrayIterator;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use League\Route\{Route as LeagueRoute, RouteGroup as LeagueRouteGroup, Router as LeagueRouter};
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use tiFy\Contracts\Routing\{Route as RouteContract, RouteGroup as RouteGroupContract, Router as RouterContract};
use tiFy\Http\RedirectResponse;
use tiFy\Routing\Concerns\{ContainerAwareTrait, RegisterMapAwareTrait};
use Zend\HttpHandlerRunner\Emitter\EmitterInterface;

class Router extends LeagueRouter implements RouterContract
{
    use ContainerAwareTrait, RegisterMapAwareTrait;

    /**
     * Instance de la route associée à la requête HTTP courante.
     * @var Route
     */
    protected $current;

    /**
     * Liste des routes déclarées.
     * @var Route[]
     */
    protected $items;

    /**
     * Préfixe du chemin des routes XHR (requête Ajax XmlHttpRequest).
     * @var string
     */
    protected $xhrPrefix = 'xhr';

    /**
     * CONSTRUCTEUR.
     *
     * @param ContainerInterface $container Instance du conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);

        parent::__construct();
    }

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
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        if (is_null($this->getStrategy())) :
            $this->setStrategy($this->getContainer()->get('router.strategy.default'));
        endif;

        return parent::dispatch($request);
    }

    /**
     * @inheritdoc
     */
    public function emit(ResponseInterface $response): void
    {
        /** @var EmitterInterface $emitter */
        $emitter = $this->getContainer()->get('router.emitter');

        $emitter->emit($response);
    }

    /**
     * @inheritdoc
     */
    public function exists(): bool
    {
        return !empty($this->items);
    }

    /**
     * {@inheritdoc}
     *
     * @return RouteGroupContract
     */
    public function group(string $prefix, callable $group): LeagueRouteGroup
    {
        $group = new RouteGroup($prefix, $group, $this);
        $this->groups[] = $group;

        return $group;
    }

    /**
     * {@inheritdoc}
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
        $path = sprintf('/%s', ltrim(url()->rewriteBase() . sprintf('/%s', ltrim($path, '/')), '/'));

        $route = new Route($method, $path, $handler, $this);

        $this->routes[] = $route;

        return $route;
    }

    /**
     * @inheritdoc
     */
    public function parseRoutePath(string $path): string
    {
        return preg_replace(array_keys($this->patternMatchers), array_values($this->patternMatchers), $path);
    }

    /**
     * @inheritdoc
     *
     * @return RouteContract
     */
    public function post($path, $handler): LeagueRoute
    {
        return parent::post($path, $handler);
    }

    /**
     * @inheritdoc
     */
    public function redirect(string $name, array $parameters = [], int $status = 302): void
    {
        if ($to = $this->url($name, $parameters)) {
            $response = RedirectResponse::createPsr($to, $status);

            $this->emit($response);
        }
    }

    /**
     * @inheritdoc
     */
    public function url(string $name, array $parameters = [], bool $absolute = true): ?string
    {
        try {
            $route = $this->getNamedRoute($name);

            try {
                return $route->getUrl($parameters, $absolute);
            } catch (LogicException $e) {
                throw new LogicException($e->getMessage(), $e->getCode());
            }
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @inheritdoc
     */
    public function xhr(string $path, $handler, string $method = 'POST'): RouteContract
    {
        return $this->map($method, $this->xhrPrefix . '/' . ltrim($path, '/'), $handler);
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
    protected function prepRoutes(ServerRequestInterface $request): void
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
}