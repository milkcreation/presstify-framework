<?php

namespace tiFy\Routing;

use League\Uri\Http;
use League\Uri\Modifiers\RemoveQueryParams;
use League\Uri\Modifiers\AppendQuery;
use League\Uri\Components\Query;
use tiFy\Contracts\Kernel\Request;
use tiFy\Contracts\Routing\Router;
use tiFy\Contracts\Routing\Url as UrlContract;

class Url implements UrlContract
{
    /**
     * Instance du controleur de requête HTTP.
     * @var Router
     */
    protected $request;

    /**
     * Instance du controleur de routage.
     * @var Router
     */
    protected $router;

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct(Router $router, Request $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    /**
     * Récupération de l'url propre. Nettoyée de la liste des arguments à exclure par défaut.
     *
     * @return string
     */
    public function clean()
    {
        return $this->without($this->cleanArgs(), $this->full());
    }

    /**
     * Liste des arguments à exclure de l'url propre.
     *
     * @return array
     */
    public function cleanArgs()
    {
        return wp_removable_query_args();
    }

    /**
     * Récupération de l'url courante. Sans les arguments de requête.
     *
     * @return string
     */
    public function current()
    {
        return $this->request->getUriForPath('');
    }

    /**
     * Récupération de l'url courante complète. Arguments de requête inclus.
     *
     * @return string
     */
    public function full()
    {
        return $this->request->fullUrl();
    }

    /**
     * Récupération d'une url agrémentée d'une liste d'arguments de requête.
     *
     * @param string[] $args Liste des arguments de requête à inclure.
     * @param string $url Url à nettoyer. Url propre par défaut.
     *
     * @return string
     */
    public function with(array $args, string $url = '')
    {
        $url = $url ? : $this->clean();

        return (string)(new AppendQuery(Query::createFromPairs($args)))->process(Http::createFromString($url));
    }

    /**
     * Récupération d'une url nettoyée d'une liste d'arguments de requête.
     *
     * @param string[] $args Liste des arguments de requête à exclure.
     * @param string $url Url à nettoyer. Url propre par défaut.
     *
     * @return string
     */
    public function without(array $args, string $url = '')
    {
        $url = $url ? : $this->clean();

        return (string)(new RemoveQueryParams($args))->process(Http::createFromString($url));
    }
}