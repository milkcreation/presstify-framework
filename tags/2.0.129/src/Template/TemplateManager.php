<?php declare(strict_types=1);

namespace tiFy\Template;

use tiFy\Contracts\Template\TemplateFactory as TemplateFactoryContract;
use tiFy\Contracts\Template\TemplateManager as TemplateManagerContract;
use tiFy\Support\Manager;

class TemplateManager extends Manager implements TemplateManagerContract
{
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
    public function get($name): ?TemplateFactoryContract
    {
        return parent::get($name);
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