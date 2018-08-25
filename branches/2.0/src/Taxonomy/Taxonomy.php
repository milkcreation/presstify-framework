<?php

namespace tiFy\Taxonomy;

use tiFy\App\AppController;

final class Taxonomy extends AppController
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
        if ($taxonomies = $this->appConfig(null, [])) :
            foreach ($taxonomies as $name => $attrs) :
                $this->register($name, $attrs);
            endforeach;
        endif;

        do_action('tify_taxonomy_register', $this);
    }

    /**
     * Création d'une taxonomie personnalisée.
     *
     * @param string $name Nom de qualification de la taxonomie.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return null|TaxonomyController
     */
    public function register($name, $attrs = [])
    {
        $alias = "tfy.taxonomy.{$name}";
        if($this->appServiceHas($alias)) :
            return;
        endif;

        $this->appServiceShare($alias, new TaxonomyController($name, $attrs, $this));

        return $this->appServiceGet($alias);
    }

    /**
     * Récupération d'un controleur de taxonomie.
     *
     * @param $name Nom de qualification du controleur.
     *
     * @return null|TaxonomyController
     */
    public function get($name)
    {
        $alias = "tfy.taxonomy.{$name}";
        if($this->appServiceHas($alias)) :
            return $this->appServiceGet($alias);
        endif;
    }
}