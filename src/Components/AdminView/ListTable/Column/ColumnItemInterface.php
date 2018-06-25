<?php

namespace tiFy\Components\AdminView\ListTable\Column;

use ArrayAccess;
use IteratorAggregate;
use tiFy\Components\AdminView\ListTable\Item\ItemInterface;

interface ColumnItemInterface extends ArrayAccess, IteratorAggregate
{
    /**
     * Affichage.
     *
     * @param ItemInterface $item Données de l'élément courant à afficher.
     *
     * @return string
     */
    public function display($item);

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Récupération de l'entête au format HTML.
     *
     * @param bool $with_id Activation de l'id de la balise HTML.
     *
     * @return string
     */
    public function getHeader($with_id = true);

    /**
     * Vérification de maquage de la colonne.
     *
     * @return bool
     */
    public function isHidden();

    /**
     * Vérifie si la colonne est la colonne principale.
     *
     * @return bool
     */
    public function isPrimary();

    /**
     * Vérifie si la colonne peut être ordonnancée.
     *
     * @return bool
     */
    public function isSortable();
}