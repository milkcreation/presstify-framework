<?php declare(strict_types=1);

namespace tiFy\Template;

use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Contracts\Template\TemplateManager as TemplateManagerContract;

class TemplateManager implements TemplateManagerContract
{
    /**
     * Liste des éléments déclarés.
     * @var TemplateFactory[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        foreach (config('template', []) as $name => $attrs) :
            $this->register($name, $attrs);
        endforeach;
    }

    /**
     * @inheritdoc
     */
    public function get(string $name): ?TemplateFactory
    {
        return $this->items[$name] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function register(string $name, array $attrs = []): TemplateManagerContract
    {
        $this->set($name, app()->get('template.factory', [$attrs]));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function resourcesDir(?string $path = ''): ?string
    {
        $path = $path ? '/Resources/' . ltrim($path, '/') : '/Resources';

        return file_exists(__DIR__ . $path) ? __DIR__ . $path : '';
    }

    /**
     * @inheritdoc
     */
    public function resourcesUrl(?string $path = ''): ?string
    {
        $cinfo = class_info($this);
        $path = '/Resources/' . ltrim($path, '/');

        return file_exists($cinfo->getDirname() . $path) ? class_info($this)->getUrl() . $path : '';
    }

    /**
     * @inheritdoc
     */
    public function set(string $name, TemplateFactory $template): TemplateManagerContract
    {
        $this->items[$name] = $this->items[$name] ?? call_user_func($template, $name);

        return $this;
    }
}