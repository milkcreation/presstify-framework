<?php

namespace tiFy\Core\Label;

use tiFy\Apps\AppController;
use tiFy\Label\LabelController;

final class Label extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        $this->appAddAction('init', null, 9);
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        if ($labels = $this->appConfig()) :
            foreach ($labels as $name => $attrs) :
                $this->register($name, $attrs);
            endforeach;
        endif;

        do_action('tify_label_register', $this);
    }

    /**
     * Déclaration de controleur d'intitulés.
     *
     * @param $name Nom de qualification du controleur.
     * @param array $attrs Attributs de configuration.
     *
     * @return null|LabelController
     */
    public function register($name, $attrs = [])
    {
        $alias = "tfy.label.{$name}";
        if($this->appServiceHas($alias)) :
            return;
        endif;

        $this->appServiceShare($alias, new LabelController($name, $attrs));

        return $this->appServiceGet($alias);
    }

    /**
     * Récupération d'un controleur d'intitulés.
     *
     * @param $name Nom de qualification du controleur.
     *
     * @return null|LabelController
     */
    public function get($name)
    {
        $alias = "tfy.label.{$name}";
        if($this->appServiceHas($alias)) :
            return $this->appServiceGet($alias);
        endif;
    }
}