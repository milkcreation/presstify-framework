<?php

namespace tiFy\Taxonomy;

use tiFy\Contracts\Taxonomy\TaxonomyItemInterface;

final class Taxonomy
{
    /**
     * Liste des types de post déclarés.
     * @var TaxonomyItemInterface[]
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
                foreach (config('taxonomy', []) as $name => $attrs) :
                    $this->register($name, $attrs);
                endforeach;
            },
            0
        );
    }

    /**
     * Création d'une taxonomie personnalisée.
     *
     * @param string $name Nom de qualification de la taxonomie.
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return TaxonomyItemInterface
     */
    public function register($name, $attrs = [])
    {
        if(!isset($this->items[$name])) :
            return app()->resolve(TaxonomyItemController::class, [$name, $attrs]);
        else :
            return $this->items[$name];
        endif;
    }

    /**
     * Récupération d'une instance de controleur de taxonomie.
     *
     * @param $name Nom de qualification du controleur.
     *
     * @return null|TaxonomyItemInterface
     */
    public function get($name)
    {
        return isset($this->items['name']) ? $this->items['name'] : null;
    }
}