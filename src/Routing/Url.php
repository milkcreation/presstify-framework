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
     * Sous arborescence du chemin de l'url.
     * @var string
     */
    protected $rewriteBase;

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
     * {@inheritdoc}
     */
    public function clean()
    {
        return $this->without($this->cleanArgs(), $this->full());
    }

    /**
     * {@inheritdoc}
     */
    public function cleanArgs()
    {
        return wp_removable_query_args();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->request->getUriForPath('');
    }

    /**
     * {@inheritdoc}
     */
    public function full()
    {
        return $this->request->fullUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function rewriteBase()
    {
        if( is_null($this->rewriteBase)) :
            $this->rewriteBase = preg_replace(
                '#^' .preg_quote(request()->getSchemeAndHttpHost()) . '#', '', env('SITE_URL')
            );
        endif;

        return $this->rewriteBase;
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $args, string $url = '')
    {
        $url = $url ? : $this->clean();

        return (string)(new AppendQuery(Query::createFromPairs($args)))->process(Http::createFromString($url));
    }

    /**
     * {@inheritdoc}
     */
    public function without(array $args, string $url = '')
    {
        $url = $url ? : $this->clean();

        return (string)(new RemoveQueryParams($args))->process(Http::createFromString($url));
    }
}