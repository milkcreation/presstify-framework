<?php

namespace tiFy\Db;

use tiFy\Apps\AppController;

final class Db extends AppController
{
    /**
     * Liste des controleurs de bases de données déclarées.
     * @var Table
     */
    protected $registered = [];

    /**
     * Classe de rappel
     * @var unknown
     */
    public static $Query = null;

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('init', null, 9);
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    final public function init()
    {
        foreach($this->appConfig(null, []) as $name => $attrs) :
            $this->register($name, $args);
        endforeach;

        do_action('tify_db_register', $this);
    }

    /**
     * Déclaration de controleur de base de données.
     *
     * @param string $name Nom de qualification du controleur de base de données.
     * @param array $attrs Attributs de configuration de la base de données
     *
     * @return DbController
     */
    public function register($name, $attrs = [])
    {
        $alias = "tfy.db.{$name}";
        if($this->appServiceHas($alias)) :
            return;
        endif;

        $this->appServiceShare($alias, new DbController($name, $attrs));

        return $this->appServiceGet($alias);
    }

    /**
     * Récupération d'un controleur de base de données.
     *
     * @param string $name Nom de qualification du controleur de base de données.
     *
     * @return null|DbController
     */
    public function get($name)
    {
        $alias = "tfy.db.{$name}";
        if ($this->appServiceHas($alias)) :
            $this->appServiceGet($alias);
        endif;
    }
}