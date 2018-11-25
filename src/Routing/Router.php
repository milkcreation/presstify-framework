<?php

namespace tiFy\Routing;

use ArrayIterator;
use Illuminate\Support\Arr;
use League\Route\RouteCollection;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Strategy\JsonStrategy;
use League\Route\Strategy\StrategyInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
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
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $container->add(RouteHandlerContract::class, function ($name, $attrs = [], $router) {
            return new RouteHandler($name, $attrs, $this);
        });

        parent::__construct($container);
    }

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
    public function current()
    {
        return $this->hasCurrent() ? $this->current : null;
    }

    /**
     * {@inheritdoc}
     */
    public function currentRouteName()
    {
        return $this->hasCurrent() ? $this->current->getName() : '';
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
     */
    public function getNamedRoute($name)
    {
        return parent::getNamedRoute($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getPatternMatchers()
    {
        return $this->patternMatchers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCurrent()
    {
        return $this->current instanceof RouteContract;
    }

    /**
     * {@inheritdoc}
     */
    public function isCurrentNamed($name)
    {
        return $this->hasCurrent() && ($this->current->getName() === $name);
    }

    /**
     * {@inheritdoc}
     */
    public function map($method, $path, $handler)
    {
        $path = sprintf('/%s', ltrim(request()->getBaseUrl() . $path, '/'));

        $route = (new Route($this))->setMethods((array)$method)->setPath($path)->setCallable($handler);

        $this->routes[] = $route;

        return $route;
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
                // 'strategy' => ''
            ],
            $attrs
        );
        extract($attrs);

        $method = ($method === 'any')
            ? ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS']
            : array_map('strtoupper', Arr::wrap($method));
        $scheme = $scheme ?? request()->getScheme();
        $host = $host ?? request()->getHost();
        $strategy = $strategy ?? 'html';

        if (!$strategy instanceof StrategyInterface) :
            switch($strategy) :
                default :
                case 'html' :
                    $strategy = new ApplicationStrategy();
                    break;
                case 'json':
                    $strategy = new JsonStrategy();
                    break;
            endswitch;
        endif;

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