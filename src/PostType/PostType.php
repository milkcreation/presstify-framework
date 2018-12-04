<?php

namespace tiFy\PostType;

use tiFy\Contracts\PostType\PostTypeItemInterface;

final class PostType
{
    /**
     * Liste des types de post déclarés.
     * @var PostTypeItemInterface[]
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
     * Création d'un type de post personnalisé.
     *
     * @param string $name Nom de qualification du type de post.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return PostTypeItemInterface
     */
    public function register($name, $attrs = [])
    {
        return $this->items[$name] = $this->items[$name]
            ?? app()->resolve(PostTypeItemController::class, [$name, $attrs]);
    }

    /**
     * Récupération d'une instance de controleur de type de post.
     *
     * @param $name Nom de qualification du controleur.
     *
     * @return null|PostTypeItemInterface
     */
    public function get($name)
    {
        return $this->items[$name] ?? null;
    }
}