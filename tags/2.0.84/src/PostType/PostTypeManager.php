<?php

namespace tiFy\PostType;

use tiFy\Contracts\PostType\PostTypeManager as PostTypeManagerContract;
use tiFy\Contracts\PostType\PostTypeFactory;

final class PostTypeManager implements PostTypeManagerContract
{
    use PostTypeResolverTrait;

    /**
     * Liste des types de post déclarés.
     * @var PostTypeFactory[]
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
        return app()->get("post-type.{$alias}", $args);
    }
}