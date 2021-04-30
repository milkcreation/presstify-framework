<?php

declare(strict_types=1);

namespace tiFy\Routing;

use Pollen\Routing\RouterInterface;
use tiFy\Contracts\Http\Request as RequestContract;
use tiFy\Contracts\Routing\Url as UrlContract;
use tiFy\Contracts\Routing\UrlFactory as UrlFactoryContract;
use tiFy\Http\Request;

class Url extends UrlFactory implements UrlContract
{
    /**
     * Instance des requêtes HTTP.
     * @var RequestContract
     */
    protected $request;

    /**
     * Sous arborescence du chemin de l'url racine.
     * @var string
     */
    protected $rewriteBase;

    /**
     * Instance du router.
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param RouterInterface $router
     * @param RequestContract|null $request
     *
     * @return void
     */
    public function __construct(RouterInterface $router, ?RequestContract $request = null)
    {
        $this->router = $router;
        $this->request = $request ?? Request::createFromGlobals();

        parent::__construct(app()->runningInConsole() ? (string)$this->root() : $this->request->fullUrl());
    }

    /**
     * @inheritDoc
     */
    public function current(bool $full = true): UrlFactoryContract
    {
        return new UrlFactory($full ? $this->request->fullUrl() : $this->request->url());
    }

    /**
     * @inheritDoc
     */
    public function rel(string $url): ?string
    {
        $root = $this->root()->render();

        return preg_match('/^' . preg_quote($root, '/') . '/', $url)
            ? '/' . ltrim(preg_replace('/^' . preg_quote($root, '/') . '/', '', $url), '/')
            : null;
    }

    /**
     * @inheritDoc
     */
    public function rewriteBase(): string
    {
        if (is_null($this->rewriteBase)) {
            $this->rewriteBase = preg_replace(
                '/^' . preg_quote($this->request->getSchemeAndHttpHost(), '/') . '/', '', $this->root()->render()
            );
        }

        return $this->rewriteBase;
    }

    /**
     * @inheritDoc
     */
    public function root(?string $path = null): UrlFactoryContract
    {
        return new UrlFactory(config('app_url', $this->request->root()) . ($path ? '/' . ltrim($path, '/') : ''));
    }

    /**
     * @inheritDoc
     */
    public function scope(): string
    {
        return ($scope = $this->root()->path()) ? '/' . rtrim(ltrim($scope, '/'), '/') . '/' : '/';
    }
}