<?php

namespace tiFy\Taxonomy;

use tiFy\Contracts\Taxonomy\TaxonomyManager as TaxonomyManagerContract;
use tiFy\Contracts\Taxonomy\TaxonomyFactory;

final class TaxonomyManager implements TaxonomyManagerContract
{
    use TaxonomyResolverTrait;

    /**
     * Liste des types de post déclarés.
     * @var TaxonomyFactory[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->manager = $this;
    }

    /**
     * @inheritdoc
     */
    public function get($name)
    {
        return $this->items[$name] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function register($name, $attrs = [])
    {
        return $this->items[$name] = $this->items[$name] ?? $this->resolve('factory', [$name, $attrs]);
    }

    /**
     * @inheritdoc
     */
    public function resolve($alias, $args = [])
    {
        return app()->get("taxonomy.{$alias}", $args);
    }
}