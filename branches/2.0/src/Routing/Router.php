<?php

namespace tiFy\Routing;

use ArrayIterator;
use Illuminate\Support\Arr;
use League\Route\RouteCollection;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Http\Message\ResponseInterface;
use tiFy\Contracts\Routing\Route as RouteContract;
use tiFy\Contracts\Routing\RouteHandler as RouteHandlerContract;
use tiFy\Contracts\Routing\Router as RouterContract;
use Zend\Diactoros\Response\SapiEmitter;

class Router extends RouteCollection implements RouterContract
{
    /**
     * Attributs de la route courante.
     * @var null|RouteContract
     */
    protected $current;

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->routes);
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
        return !empty($this->routes);
    }

    /**
     * {@inheritdoc}
     *
     * @return Route
     */
    public function getNamedRoute($name)
    {
        return parent::getNamedRoute($name);
    }

    /**
     * {@inheritdoc}
     */
    public function map($method, $path, $handler)
    {
        $path = sprintf('/%s', ltrim(request()->getBaseUrl() . $path, '/'));
        $route = (new Route())->setMethods((array)$method)->setPath($path)->setCallable($handler);

        $this->routes[] = $route;

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrent($name, $args = [])
    {
        try {
            $route = $this->getNamedRoute($name);
            $route->setCurrent(true);
            $route->setArgs($args);

            $this->current = $route;
        } catch (\Exception $e) {

        }
    }

    /**
     * {@inheritdoc}
     */
    public function register($name, $attrs = [])
    {
        $attrs = array_merge(
            [
                'method' => 'any',
                'path' => '/',
                'cb' => '',
                // @todo 'group'    => '',
                // 'scheme' => '',
                // 'host' => '',
                //'strategy' => ''
            ],
            $attrs
        );
        extract($attrs);

        $method = ($method === 'any')
            ? ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS']
            : array_map('strtoupper', Arr::wrap($method));
        $scheme = $scheme ?? request()->getScheme();
        $host = $host ?? request()->getHost();
        $strategy = $strategy ?? new ApplicationStrategy();

        return $this->map(
            $method,
            $path,
            app()->get(RouteHandlerContract::class, [$name, $attrs, $this])
        )
            ->setName($name)
            ->setScheme($scheme)
            ->setHost($host)
            ->setStrategy($strategy);
    }

    /**
     * Récupération de l'itérateur.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->routes);
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
        return array_key_exists($key, $this->routes);
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
        return $this->routes[$key];
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
            $this->routes[] = $value;
        else :
            $this->routes[$key] = $value;
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
        unset($this->routes[$key]);
    }
}