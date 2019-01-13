<?php

namespace tiFy\Wp;

use tiFy\Contracts\Wp\WpManager as WpManagerContract;

class WpManager implements WpManagerContract
{
    use WpResolverTrait;

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        $this->manager = $this;

        if ($this->is()) :
            config(['site_url' => site_url()]);
        endif;
    }

    /**
     * @inheritdoc
     */
    public function is()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function resolve($alias, $args = [])
    {
        return app()->get("wp.{$alias}", $args);
    }
}