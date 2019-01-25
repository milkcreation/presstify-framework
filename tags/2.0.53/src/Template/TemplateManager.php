<?php

namespace tiFy\Template;

use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Contracts\Template\TemplateManager as TemplateManagerContract;

class TemplateManager implements TemplateManagerContract
{
    /**
     * Liste des éléments déclarés.
     * @var array
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
     * {@inheritdoc}
     */
    public function register($name, $attrs = [])
    {
        return $this->set($name, $this->items[$name] ?? app()->get('template.factory', [$name, $attrs]));
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, TemplateFactory $template)
    {
        return $this->items[$name] = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return $this->items[$name] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function resourcesDir($path = '')
    {
        $path = $path ? '/Resources/' . ltrim($path, '/') : '/Resources';

        return file_exists(__DIR__ . $path) ? __DIR__ . $path : '';
    }

    /**
     * {@inheritdoc}
     */
    public function resourcesUrl($path = '')
    {
        $cinfo = class_info($this);
        $path = '/Resources/' . ltrim($path, '/');

        return file_exists($cinfo->getDirname() . $path) ? class_info($this)->getUrl() . $path : '';
    }
}