<?php declare(strict_types=1);

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
     * @param string $method
     * @param string $path
     * @param callable $handler
     * @param Router $router Instance du controleur de route.
     *
     * @return void
     */
    public function __construct(string $method, string $path, $handler, $router)
    {
        $this->router = $router;

        parent::__construct($method, $path, $handler);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(array $params = [], bool $absolute = true): string
    {
        $routes = (new RouteParser())->parse($this->router->parseRoutePath($this->getPath()));

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
                    $host = $this->getHost() ? : request()->getHost();
                    $port = $this->getPort() ? : request()->getPort();
                    $scheme = $this->getScheme() ? : request()->getScheme();
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
    public function isCurrent(): bool
    {
        return $this->current;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrent()
    {
        $this->current = true;
    }
}