<?php

namespace tiFy\View;

use tiFy\Contracts\View\ViewPattern as ViewPatternContract;
use tiFy\Contracts\View\ViewPatternController;

class ViewPattern implements ViewPatternContract
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
        foreach (config('view.pattern', []) as $name => $attrs) :
            $this->register($name, $attrs);
        endforeach;
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
    public function register($name, $attrs = [])
    {
        return $this->set($name, $this->items[$name] ?? app()->get('view.pattern.controller', [$name, $attrs]));
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

    /**
     * {@inheritdoc}
     */
    public function set($name, ViewPatternController $pattern)
    {
        return $this->items[$name] = $pattern;
    }
}