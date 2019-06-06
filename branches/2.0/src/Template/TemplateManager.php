<?php declare(strict_types=1);

namespace tiFy\Template;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\Container\Container;
use tiFy\Contracts\Template\{TemplateFactory as TemplateFactoryContract, TemplateManager as TemplateManagerContract};
use tiFy\Support\Manager;
use tiFy\Support\Proxy\Router;

class TemplateManager extends Manager implements TemplateManagerContract
{
    /**
     * Url de base de routage.
     * @var string
     */
    public $baseUrl = '';

    /**
     * Liste des éléments déclarés.
     * @var TemplateFactoryContract[]
     */
    protected $items = [];

    /**
     * Préfixe de urls de routage.
     * @var string
     */
    public $urlPrefix = '/';

    /**
     * {@inheritDoc}
     *
     * @return TemplateFactoryContract|null
     */
    public function get($name): ?TemplateFactoryContract
    {
        return parent::get($name);
    }

    /**
     * {@inheritDoc}
     *
     * @return Container|null
     */
    public function getContainer(): ?ContainerInterface
    {
        return parent::getContainer();
    }

    /**
     * @inheritDoc
     */
    public function httpCacheController(string $name, string $path, ServerRequestInterface $psrRequest)
    {
        return $this->get($name)->httpCacheController($path, $psrRequest);
    }

    /**
     * @inheritDoc
     */
    public function httpController(string $name, ServerRequestInterface $psrRequest)
    {
        return $this->get($name)->httpController($psrRequest);
    }

    /**
     * @inheritDoc
     */
    public function httpXhrcontroller(string $name, ServerRequestInterface $psrRequest)
    {
        return $this->get($name)->httpXhrController($psrRequest);
    }

    /**
     * @inheritDoc
     */
    public function prepareRoutes(): TemplateManagerContract
    {
        $this->baseUrl = md5('tify:template'); // 7855ce7d975d5a1ede9b5a83d7235dee

        $base_url = '/' . rtrim(ltrim($this->urlPrefix, '/'), '/') . '/' . $this->baseUrl;

        foreach(['head', 'delete', 'get', 'options', 'post', 'put', 'patch'] as $method) {
            Router::$method($base_url . '/{name}', [$this, 'httpController']);
            Router::xhr($base_url . '/{name}/xhr', [$this, 'httpXhrController'], $method);
        }
        Router::get($base_url . '/{name}/cache/{path:.*}', [$this, 'httpCacheController'])->strategy('app');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function register($name, ...$args): TemplateManagerContract
    {
        return $this->set([$name => $args[0] ?? []]);
    }

    /**
     * @inheritDoc
     */
    public function resourcesDir(?string $path = ''): ?string
    {
        $path = $path ? '/Resources/' . ltrim($path, '/') : '/Resources';

        return file_exists(__DIR__ . $path) ? __DIR__ . $path : '';
    }

    /**
     * @inheritDoc
     */
    public function resourcesUrl(?string $path = ''): ?string
    {
        $cinfo = class_info($this);
        $path = '/Resources/' . ltrim($path, '/');

        return file_exists($cinfo->getDirname() . $path) ? class_info($this)->getUrl() . $path : '';
    }

    /**
     * @inheritDoc
     */
    public function setUrlPrefix(string $prefix): TemplateManagerContract
    {
        $this->urlPrefix = $prefix;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function walk(&$item, $key = null): void
    {
        if (!$item instanceof TemplateFactory) {
            $attrs = $item;
            /* @var TemplateFactoryContract: $item */
            $item = $this->getContainer()->get(TemplateFactoryContract::class);
            $item->set($attrs);
        }
        $item->setInstance((string)$key, $this);
    }
}