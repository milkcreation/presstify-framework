<?php declare(strict_types=1);

namespace tiFy\Routing;

use FastRoute\RouteParser\Std as RouteParser;
use League\Route\Route as LeagueRoute;
use LogicException;
use tiFy\Contracts\Routing\{Route as RouteContract, Router as RouterContract};
use tiFy\Routing\Concerns\{ContainerAwareTrait, StrategyAwareTrait};

class Route extends LeagueRoute implements RouteContract
{
    use ContainerAwareTrait, StrategyAwareTrait;

    /**
     * Instance du controleur de gestion des routes.
     * @return RouterContract
     */
    protected $collection;

    /**
     * Indicateur de route en réponse à la requête HTTP courante.
     * @var boolean
     */
    protected $current = false;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $method
     * @param string $path
     * @param callable $handler
     * @param Router $collection Instance du controleur de gestion des routes.
     *
     * @return void
     */
    public function __construct(string $method, string $path, $handler, $collection)
    {
        $this->collection = $collection;

        parent::__construct(strtoupper($method), $path, $handler);

        $this->setContainer($this->collection->getContainer());
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(array $params = [], bool $absolute = true): string
    {
        $routes = (new RouteParser())->parse($this->collection->parseRoutePath($this->getPath()));

        foreach ($routes as $route) :
            $url = '';
            $paramIdx = 0;
            foreach ($route as $part) :
                if (is_string($part)) :
                    $url .= $part;
                    continue;
                endif;

                if ($paramIdx === count($params)) :
                    throw new LogicException(__('Le nombre de paramètres fournis est insuffisant.', 'tify'));
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

        throw new LogicException(__('Le nombre de paramètres fournis est trop important.', 'tify'));
    }

    /**
     * @inheritdoc
     */
    public function getVar(string $key, $default = null)
    {
        return $this->vars[$key] ?? $default;
    }

    /**
     * @inheritdoc
     */
    public function isCurrent(): bool
    {
        return $this->current;
    }

    /**
     * @inheritdoc
     */
    public function setCurrent()
    {
        $this->current = true;
    }

    /**
     * @inheritdoc
     */
    public function name(string $name): RouteContract
    {
        $this->name = $name;

        return $this;
    }
}