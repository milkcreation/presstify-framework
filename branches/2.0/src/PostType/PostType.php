<?php

namespace tiFy\PostType;

use tiFy\Apps\AppController;

final class PostType extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('init', null, 0);
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        if ($post_types = $this->appConfig(null, [])) :
            foreach ($post_types as $name => $attrs) :
                $this->register($name, $attrs);
            endforeach;
        endif;

        do_action('tify_post_type_register', $this);
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

        $this->appServiceShare($alias, new PostTypeController($name, $attrs, $this));

        return $this->appServiceGet($alias);
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