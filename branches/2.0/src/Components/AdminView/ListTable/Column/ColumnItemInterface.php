<?php

namespace tiFy\Components\AdminView\ListTable\Column;

use ArrayAccess;
use IteratorAggregate;
use tiFy\Components\AdminView\ListTable\Item\ItemInterface;

interface ColumnItemInterface extends ArrayAccess, IteratorAggregate
{
    /**
     * Récupération de la liste des attributs de configuration.
     *
     * @return array
     */
    public function all();

    /**
     * Affichage.
     *
     * @param ItemInterface $item Données de l'élément courant à afficher.
     *
     * @return string
     */
    public function display($item);

    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indice de l'attributs. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return void
     */
    public function parse($attrs = []);

    /**
     * Définition d'un attribut de configuration.
     *
     * @param string $key Clé d'indice de l'attributs. Syntaxe à point permise.
     * @param mixed $value Valeur de de l'attribut.
     *
     * @return $this
     */
    public function set($key, $value);
}