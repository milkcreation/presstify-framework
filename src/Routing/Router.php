<?php

namespace tiFy\Routing;

use ArrayIterator;
use Illuminate\Support\Collection;
use League\Route\Router as LeagueRouter;
use League\Route\Route as LeagueRoute;
use League\Route\RouteGroup as LeagueRouteGroup;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use tiFy\Contracts\Routing\Route as RouteContract;
use tiFy\Contracts\Routing\RouteGroup as RouteGroupContract;
use tiFy\Contracts\Routing\Router as RouterContract;
use Zend\Diactoros\Response\SapiEmitter;

class Router extends LeagueRouter implements RouterContract
{
    use RouteRegisterMapTrait;

    /**
     * Instance du conteneur d'injection.
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Instance de la route associé à la requête HTTP courante.
     * @var RouteContract
     */
    protected $current;

    /**
     * Liste des routes déclarées.
     * @var RouteContract[]
     */
    protected $items;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        return new Collection($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->current = ! is_null($this->current)
            ? $this->current
            : $this->collect()->first(function (RouteContract $item) {
                return $item->isCurrent();
            });
    }

    /**
     * {@inheritdoc}
     */
    public function currentRouteName()
    {
        return $this->hasCurrent() ? $this->current()->getName() : '';
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        if (is_null($this->getStrategy())) :
            $this->setStrategy($this->getContainer()->get('router.strategy.default'));
        endif;

        return parent::dispatch($request);
    }


    /**
     * {@inheritdoc}
     */
    public function emit(ResponseInterface $response)
    {
        /** @var SapiEmitter $emitter */
        $emitter = app()->get('router.emitter');

        return $emitter->emit($response);
    }

    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        return ! empty($this->items);
    }

    /**
     * {@inheritdoc}
     *
     * @return RouteGroupContract
     */
    public function group(string $prefix, callable $group) : LeagueRouteGroup
    {
        $group = new RouteGroup($prefix, $group, $this);
        $this->groups[] = $group;

        return $group;
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCurrent()
    {
        return $this->current() instanceof RouteContract;
    }

    /**
     * {@inheritdoc}
     */
    public function isCurrentNamed($name)
    {
        return $this->currentRouteName() === $name;
    }

    /**
     * {@inheritdoc}
     *
     * @return RouteContract
     */
    public function map(string $method, string $path, $handler): LeagueRoute
    {
        $path = sprintf('/%s', ltrim(request()->getBaseUrl() . $path, '/'));

        $route = new Route($method, $path, $handler, $this);

        $this->routes[] = $route;

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function parseRoutePath(string $path): string
    {
        return preg_replace(array_keys($this->patternMatchers), array_values($this->patternMatchers), $path);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepRoutes(ServerRequestInterface $request): void
    {
        $this->processGroups($request);
        $this->buildNameIndex();

        /** @var RouteContract[] $routes */
        $this->items = $routes = array_merge(array_values($this->routes), array_values($this->namedRoutes));

        foreach ($routes as $key => $route) {
            // check for scheme condition
            if (! is_null($route->getScheme()) && $route->getScheme() !== $request->getUri()->getScheme()) :
                continue;
            endif;

            // check for domain condition
            if (! is_null($route->getHost()) && $route->getHost() !== $request->getUri()->getHost()) :
                continue;
            endif;

            // check for port condition
            if (! is_null($route->getPort()) && $route->getPort() !== $request->getUri()->getPort()) :
                continue;
            endif;

            if (is_null($route->getStrategy())) :
                if (($group = $route->getParentGroup()) && ! is_null($group->getStrategy())) :
                    $route->setStrategy($group->getStrategy());
                else :
                    $route->setStrategy($this->getStrategy());
                endif;
            endif;

            $this->addRoute($route->getMethod(), $this->parseRoutePath($route->getPath()), $route);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function redirect($name, $parameters = [], $status = 302)
    {
        if ($to = $this->url($name, $parameters)) :
            $response = (new DiactorosFactory())->createResponse(redirect($to, $status));

            $this->emit($response);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function url($name, $parameters = [], $absolute = true)
    {
        try {
            $route = $this->getNamedRoute($name);

            try {
                return $route->getUrl($parameters, $absolute);
            } catch (\Exception $e) {
                return wp_die(
                    sprintf(
                        __('<h1>Récupération d\'url de routage : %s</h1><p>%s</p>', 'tify'),
                        $name,
                        $e->getMessage()
                    ),
                    "routerUrl > route : {$name}",
                    500
                );
            }
        } catch (\Exception $e) {
            return wp_die(
                sprintf(
                    __('<h1>Récupération d\'url de routage : %s</h1><p>%s</p>', 'tify'),
                    $name,
                    $e->getMessage()
                ),
                "routerUrl > route : {$name}",
                500
            );
        }
    }

    /**
     * Récupération de l'itérateur.
     *
     * @return ArrayIterator
     */
    public function getIterator()
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
    public function offsetExists($key)
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
    public function offsetSet($key, $value)
    {
        if (is_null($key)) :
            $this->items[] = $value;
        else :
            $this->items[$key] = $value;
        endif;
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
}