<?php

namespace tiFy\Routing;

use FastRoute\RouteParser\Std as RouteParser;
use League\Route\Route as LeagueRoute;
use tiFy\Contracts\Routing\Route as RouteContract;
use tiFy\Contracts\Routing\Router;

class Route extends LeagueRoute implements RouteContract
{
    /**
     * Instance du controleur de gestion des routes.
     * @return Router
     */
    protected $router;

    /**
     * Indicateur de route en réponse à la requête HTTP courante.
     * @var boolean
     */
    protected $current = false;

    /**
     * Liste des variables passées en arguments.
     * @var array
     */
    protected $args = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param Router $router Instance du controleur de route.
     *
     * @return void
     */
    public function __construct($router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * {@inheritdoc}
     */
    public function getPattern()
    {
        $matchers = $this->router->getPatternMatchers();

        return preg_replace(array_keys($matchers), array_values($matchers), $this->getPath());
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($params = [], $absolute = true)
    {
        $routes = (new RouteParser())->parse($this->getPattern());

        foreach ($routes as $route) :
            $url = '';
            $paramIdx = 0;
            foreach ($route as $part) :
                if (is_string($part)) :
                    $url .= $part;
                    continue;
                endif;

                if ($paramIdx === count($params)) :
                    throw new \LogicException(__('Le nombre de paramètres fournis est insuffisant.', 'tify'));
                endif;
                $url .= $params[$paramIdx++];
            endforeach;

            if ($paramIdx === count($params)) :
                if ($absolute) :
                    $host = $this->getHost();
                    $port = $this->getPort();
                    $scheme = $this->getScheme();
                    if ((($port === 80) && ($scheme = 'http')) || (($port === 443) && ($scheme = 'https'))) :
                        $port = '';
                    endif;

                    $url = $scheme . '://' . $host . ($port ? ':' . $port : '') . $url;
                endif;

                return $url;
            endif;
        endforeach;

        throw new \LogicException(__('Le nombre de paramètres fournis est trop important.', 'tify'));
    }

    /**
     * {@inheritdoc}
     */
    public function isCurrent()
    {
        return $this->current();
    }

    /**
     * {@inheritdoc}
     */
    public function setArgs($args = [])
    {
        $this->args = $args;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrent()
    {
        $this->current = true;
    }
}