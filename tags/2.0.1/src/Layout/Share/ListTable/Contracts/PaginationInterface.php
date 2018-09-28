<?php

namespace tiFy\Layout\Share\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParametersBagInterface;

interface PaginationInterface extends ParametersBagInterface
{
    /**
     * Récupération de la classe HTML du conteneur de l'interface de pagination.
     *
     * @return string
     */
    public function getClass();

    /**
     * Récupération de la liste des liens de pagination.
     *
     * @return array
     */
    public function getPageLinks($which);

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
     * @return bool
     */
    public function isDisableFirst();

    /**
     * Vérification de la désactivation du lien vers la dernière page.
     *
     * @return bool
     */
    public function isDisableLast();

    /**
     * Vérification de la désactivation du lien vers la page suivante.
     *
     * @return bool
     */
    public function isDisableNext();

    /**
     * Vérification de la désactivation du lien vers la page précédente.
     *
     * @return bool
     */
    public function isDisablePrev();

    /**
     * Vérification de l'activation de la pagination par infinite scroll.
     *
     * @return bool
     */
    public function isInfiniteScroll();
}