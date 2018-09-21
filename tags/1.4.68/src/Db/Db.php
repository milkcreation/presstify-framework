<?php

namespace tiFy\Db;

use tiFy\App\AppController;
use tiFy\Db\DbControllerInterface;

final class Db extends AppController
{
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
    public function init()
    {
        foreach($this->appConfig(null, []) as $name => $attrs) :
            $this->register($name, $attrs);
        endforeach;

        do_action('tify_db_register', $this);
    }

    /**
     * Déclaration de controleur de base de données.
     *
     * @param string $name Nom de qualification du controleur de base de données.
     * @param array $attrs Attributs de configuration de la base de données
     *
     * @return DbControllerInterface
     */
    public function register($name, $attrs = [])
    {
        $alias = "tfy.db.{$name}";
        if($this->appServiceHas($alias)) :
            return;
        endif;

        $classname = isset($attrs['controller']) ? $attrs['controller'] : DbBaseController::class;

        $this->appServiceShare($alias, new $classname($name, $attrs, $this));

        return $this->appServiceGet($alias);
    }

    /**
     * Récupération d'un controleur de base de données.
     *
     * @param string $name Nom de qualification du controleur de base de données.
     *
     * @return null|DbControllerInterface
     */
    public function get($name)
    {
        $alias = "tfy.db.{$name}";
        if ($this->appServiceHas($alias)) :
            $this->appServiceGet($alias);
        endif;
    }
}