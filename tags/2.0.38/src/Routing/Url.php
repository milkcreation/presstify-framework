<?php declare(strict_types=1);

namespace tiFy\Routing;

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
    public function cleanArgs() :array
    {
        return wp_removable_query_args();
    }

    /**
     * {@inheritdoc}
     */
    public function current() : string
    {
        return $this->request->getUriForPath('');
    }

    /**
     * {@inheritdoc}
     */
    public function format(string $format = 'RFC3986', string $url = '') : string
    {
        return url_factory($url ?: $this->clean())->format($format)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function full() : string
    {
        return $this->request->fullUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function rewriteBase() : string
    {
        if (is_null($this->rewriteBase)) :
            $this->rewriteBase = $this->request->server->has('CONTEXT_PREFIX')
                ? $this->request->server->get('CONTEXT_PREFIX')
                : preg_replace(
                    '#^' . preg_quote($this->request->getSchemeAndHttpHost()) . '#', '', $this->root()
                );
        endif;

        return $this->rewriteBase;
    }

    /**
     * {@inheritdoc}
     */
    public function root(string $path = '') : string
    {
        $path = $path ? '/' . ltrim($path, '/') : '';

        return config('site_url') . $path;
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $args, string $url = '') : string
    {
        return url_factory($url ?: $this->clean())->with($args)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function without(array $args, string $url = '') : string
    {
        return url_factory($url ?: $this->clean())->without($args)->get();
    }
}