<?php

namespace tiFy\Db;

use tiFy\Contracts\Db\DbItemInterface;

final class Db
{
    /**
     * Liste des instances déclarées.
     * @var DbItemInterface[]
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
                foreach(config('db', []) as $name => $attrs) :
                    $this->register($name, $attrs);
                endforeach;
            },
            9
        );
    }

    /**
     * Ajout d'un élément.
     *
     * @param string $name Nom de qualification de l'éléments.
     * @param array $attrs Liste des attributs de configuration de l'élément.
     *
     * @return $this
     */
    public function add($name, $attrs = [])
    {
        config()->set(
            'db',
            array_merge(
                [$name => $attrs],
                config('db', [])
            )
        );

        return $this;
    }

    /**
     * Déclaration de controleur de base de données.
     *
     * @param string $name Nom de qualification du controleur de base de données.
     * @param array $attrs Attributs de configuration de la base de données
     *
     * @return DbItemInterface
     */
    protected function register($name, $attrs = [])
    {
        if ($item = $this->get($name)) :
            return $item;
        endif;

        $controller = isset($attrs['controller']) ? $attrs['controller'] : DbItemBaseController::class;
        $resolved = new $controller($name, $attrs);

        if ($resolved instanceof DbItemInterface) :
            return $this->items[$name] = $resolved;
        endif;
    }

    /**
     * Récupération d'un controleur de base de données.
     *
     * @param string $name Nom de qualification du controleur de base de données.
     *
     * @return null|DbItemInterface
     */
    public function get($name)
    {
        return isset($this->items[$name]) ? $this->items[$name] : null;
    }
}