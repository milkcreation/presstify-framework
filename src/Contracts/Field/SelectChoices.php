<?php

namespace tiFy\Contracts\Field;

use tiFy\Contracts\Kernel\Collection;

interface SelectChoices extends Collection
{
    /**
     * Résolution de sortie de la classe sous la forme d'une chaîne de caractères.
     *
     * @return string
     */
    public function __toString();

    /**
     * Récupération de la liste des éléments de la liste des éléments sélectionnés.
     *
     * @return array|SelectChoice[]
     */
    public function getSelectionChoices();

    /**
     * Traitement récursif d'encapuslation d'un élément de la liste.
     *
     * @return void
     */
    public function recursiveWrap($name, $attrs, $parent = null);

    /**
     * Affichage de la liste des éléments.
     *
     * @return string
     */
    public function render();

    /**
     * Définition de liste des éléments selectionnés.
     *
     * @param mixed $selected
     *
     * @return $this
     */
    public function setSelected($selected = null);

    /**
     * Itérateur d'affichage.
     *
     * @param SelectChoice[] $items Liste des éléments à ordonner.
     * @param int $depth Niveau de profondeur.
     * @param string $parent Nom de qualification de l'élément parent.
     *
     * @return string
     */
    public function walk($items = [], $depth = 0, $parent = null);
}