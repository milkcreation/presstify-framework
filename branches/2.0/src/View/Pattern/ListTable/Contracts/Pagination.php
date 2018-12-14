<?php

namespace tiFy\View\Pattern\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface Pagination extends ParamsBag
{
    /**
     * Résolution de sortie de la classe en tant que chaîne de caractère.
     *
     * @return string
     */
    public function __toString();

    /**
     * Affichage.
     *
     * @return array
     */
    public function display();

    /**
     * Récupération de la classe HTML du conteneur de l'interface de pagination.
     *
     * @return string
     */
    public function getClass();

    /**
     * Récupération du nombre d'éléments affiché par page.
     *
     * @return int
     */
    public function getPerPage();

    /**
     * Récupération du nombre total d'éléments.
     *
     * @return int
     */
    public function getTotalItems();

    /**
     * Récupération du nombre total de page d'éléments.
     *
     * @return int
     */
    public function getTotalPages();

    /**
     * Vérification de la désactivation du lien vers la première page.
     *
     * @return boolean
     */
    public function isDisableFirst();

    /**
     * Vérification de la désactivation du lien vers la dernière page.
     *
     * @return boolean
     */
    public function isDisableLast();

    /**
     * Vérification de la désactivation du lien vers la page suivante.
     *
     * @return boolean
     */
    public function isDisableNext();

    /**
     * Vérification de la désactivation du lien vers la page précédente.
     *
     * @return boolean
     */
    public function isDisablePrev();

    /**
     * Vérification de l'activation de la pagination par infinite scroll.
     *
     * @return boolean
     */
    public function isInfiniteScroll();

    /**
     * Définition de l'emplacement d'affichage.
     *
     * @param string $which top|bottom
     *
     * @return $this
     */
    public function which($which);
}