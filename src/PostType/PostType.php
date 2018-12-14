<?php

namespace tiFy\PostType;

use tiFy\Contracts\PostType\PostType as PostTypeContract;
use tiFy\Contracts\PostType\PostTypeFactory;

final class PostType implements PostTypeContract
{
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
        add_action(
            'init',
            function () {
                foreach (config('post-type', []) as $name => $attrs) :
                    $this->register($name, $attrs);
                endforeach;
            },
            1
        );

        add_action(
            'init',
            function() {
                global $wp_post_types;

                foreach($wp_post_types as $name => $attrs) :
                    if (!$this->get($name)) :
                        $this->register($name, get_object_vars($attrs));
                    endif;
                endforeach;
            },
            999999
        );
    }

    /**
     * {@inheritdoc}
     */
    public function register($name, $attrs = [])
    {
        return $this->items[$name] = $this->items[$name]
            ?? app()->get('post_type.factory', [$name, $attrs]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        return $this->items[$name] ?? null;
    }
}