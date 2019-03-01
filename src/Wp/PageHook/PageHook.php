<?php

namespace tiFy\Wp\PageHook;

use tiFy\Contracts\Metabox\MetaboxManager;
use tiFy\Contracts\Wp\PageHook as PageHookContract;
use tiFy\Wp\PageHook\Admin\PageHookAdminOptions;

class PageHook implements PageHookContract
{
    /**
     * Liste des éléments déclarés.
     * @var PageHookItem[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->set(config('page-hook', []));

        add_action('init', function () {
            if ($this->items) :
                /** @var MetaboxManager $metabox */
                $metabox = app('metabox');
                $metabox->add('PageHook-optionsNode', 'tify_options@options', [
                    'title'     => __('Pages d\'accroche', 'tify'),
                    'content'   => PageHookAdminOptions::class
                ]);
            endif;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->items;
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
    public function set($name, $attrs = null)
    {
        $keys = is_array($name) ? $name : [$name => $attrs];

        foreach ($keys as $k => $v) :
            $this->items[$k] = new PageHookItem($k, $v);
        endforeach;

        return $this;
    }
}