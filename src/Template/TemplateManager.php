<?php declare(strict_types=1);

namespace tiFy\Template;

use Pollen\Http\JsonResponse;
use Pollen\Http\Response;
use Pollen\Support\Proxy\RouterProxy;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use tiFy\Contracts\Container\Container;
use tiFy\Contracts\Template\{TemplateFactory as TemplateFactoryContract, TemplateManager as TemplateManagerContract};
use tiFy\Support\Manager;

class TemplateManager extends Manager implements TemplateManagerContract
{
    use RouterProxy;

    /**
     * Chemin des requêtes HTTP et XHR.
     * @var string
     */
    public $basePath = '';

    /**
     * Liste des éléments déclarés.
     * @var TemplateFactoryContract[]
     */
    protected $items = [];

    /**
     * {@inheritDoc}
     *
     * @return TemplateFactoryContract|null
     */
    public function get(...$args): ?TemplateFactoryContract
    {
        return parent::get($args[0]);
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
    public function httpXhrController(string $name, ServerRequestInterface $psrRequest)
    {
        return $this->get($name)->httpXhrController($psrRequest);
    }

    /**
     * @inheritDoc
     */
    public function prepareRoutes(): TemplateManagerContract
    {
        $this->basePath = md5('tify:template');

        foreach(['head', 'delete', 'get', 'options', 'post', 'put', 'patch'] as $method) {
            $this->router()->$method($this->basePath . '/{name}', [$this, 'httpController']);
            $this->router()->xhr($this->basePath . '/{name}/xhr', [$this, 'httpXhrController'], strtoupper($method));
        }
        $this->router()->get($this->basePath . '/{name}/cache/{path:.*}', [$this, 'httpCacheController']);

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