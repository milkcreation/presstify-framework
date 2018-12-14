<?php

namespace tiFy\View\Pattern\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface ColumnsItem extends ParamsBag
{
    /**
     * Affichage.
     *
     * @param Item $item Données de l'élément courant à afficher.
     *
     * @return string
     */
    public function display(Item $item);

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
     * Récupération du texte contenu dans l'entête.
     *
     * @return string
     */
    public function getHeaderContent();

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