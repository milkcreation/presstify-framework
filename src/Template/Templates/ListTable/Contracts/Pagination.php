<?php

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface Pagination extends ParamsBag
{
    /**
     * Rendu d'affichage de la page courante.
     *
     * @return string
     */
    public function currentPage();

    /**
     * Rendu d'affichage de l'accès à la première page.
     *
     * @return string
     */
    public function firstPage();

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
     * Rendu d'affichage de l'accès à la dernière page.
     *
     * @return string
     */
    public function lastPage();

    /**
     * Rendu d'affichage de l'accès à la page suivante.
     *
     * @return string
     */
    public function nextPage();

    /**
     * Récupération du numéro de la page courante.
     *
     * @return int
     */
    public function pageNum();

    /**
     * Récupération de l'url vers une page.
     *
     * @param int $page Numéro de la page.
     *
     * @return string
     */
    public function pagedUrl($page);

    /**
     * Rendu d'affichage de l'accès à la page précédente.
     *
     * @return string
     */
    public function prevPage();

    /**
     * Récupération de l'url de la page courante sans l'argument de pagination.
     *
     * @return string
     */
    public function unpagedUrl();

    /**
     * Définition de l'emplacement d'affichage.
     *
     * @param string $which top|bottom
     *
     * @return $this
     */
    public function which($which);
}