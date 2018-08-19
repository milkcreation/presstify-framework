<?php

namespace tiFy\PostType;

use tiFy\Apps\AppController;

final class PostType extends AppController
{
    /**
     * Liste des types de post déclarés.
     * @var array
     */
    protected $items = [];

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('init', [$this, 'preInit'], 1);
        $this->appAddAction('init', [$this, 'postInit'], 9999);
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function preInit()
    {
        if ($post_types = $this->appConfig(null, [])) :
            foreach ($post_types as $name => $attrs) :
                $this->register($name, $attrs);
            endforeach;
        endif;

        do_action('tify_post_type_register', $this);
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function postInit()
    {
        global $wp_post_types;

        foreach($wp_post_types as $name => $attrs) :
            if (!$this->get($name)) :
                $this->register($name, get_object_vars($attrs));
            endif;
        endforeach;
    }

    /**
     * Création d'un type de post personnalisé.
     *
     * @param string $name Nom de qualification du type de post.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return null|PostTypeController
     */
    public function register($name, $attrs = [])
    {
        $alias = "tfy.post_type.{$name}";
        if($this->appServiceHas($alias)) :
            return;
        endif;

        $this->appServiceShare($alias, new PostTypeItemController($name, $attrs, $this));

        return $this->items[$name] = $this->appServiceGet($alias);
    }

    /**
     * Récupération d'un controleur de type de post.
     *
     * @param $name Nom de qualification du controleur.
     *
     * @return null|PostTypeController
     */
    public function get($name)
    {
        $alias = "tfy.post_type.{$name}";
        if($this->appServiceHas($alias)) :
            return $this->appServiceGet($alias);
        endif;
    }
}